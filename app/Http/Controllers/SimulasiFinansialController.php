<?php

namespace App\Http\Controllers;

use App\Models\MitraPembiayaan;
use App\Models\ParameterSimulasiFinansial;
use App\Models\RiwayatSimulasiFinansial;
use App\Services\SimulasiFinansialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimulasiFinansialController extends Controller
{
    protected SimulasiFinansialService $service;

    public function __construct(SimulasiFinansialService $service)
    {
        $this->service = $service;
    }

    /**
     * Halaman Utama Simulasi Finansial (Siswa & Alumni).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $param = ParameterSimulasiFinansial::getActive();
        $mitras = MitraPembiayaan::aktif()->get();

        $mode = $request->query('mode', 'mandiri');
        if (! in_array($mode, ['mandiri', 'mitra'], true)) {
            $mode = 'mandiri';
        }

        // Selected partner for mitra mode
        $selectedMitraId = $request->query('mitra_id', $mitras->first()?->id);
        $selectedMitra = $mitras->firstWhere('id', $selectedMitraId) ?? $mitras->first();

        // Riwayat singkat milik pengguna
        $riwayatSingkat = $user->riwayatSimulasis()->take(5)->get();

        return view('dashboard.simulasi.index', compact(
            'user',
            'param',
            'mitras',
            'mode',
            'selectedMitra',
            'riwayatSingkat'
        ));
    }

    /**
     * Hitung Simulasi (AJAX atau Form POST).
     */
    public function hitung(Request $request)
    {
        $param = ParameterSimulasiFinansial::getActive();
        $mode = $request->input('mode', 'mandiri');

        if ($mode === 'mitra') {
            $request->validate([
                'mitra_id' => 'required|exists:mitra_pembiayaans,id',
                'biaya_keberangkatan' => 'required|numeric|min:1000000',
                'tenor_bulan' => 'required|integer|min:1',
                'gaji_jpy' => 'required|integer|min:50000',
                'kurs_jpy' => 'required|numeric|min:1',
                'biaya_hidup' => 'required|numeric|min:0',
                'pengeluaran_lain' => 'nullable|numeric|min:0',
            ]);

            $mitra = MitraPembiayaan::findOrFail($request->mitra_id);
            $hasil = $this->service->hitungMitra($request->all(), $mitra, $param);
        } else {
            $request->validate([
                'biaya_keberangkatan' => 'required|numeric|min:1000000',
                'gaji_jpy' => 'required|integer|min:50000',
                'kurs_jpy' => 'required|numeric|min:1',
                'biaya_hidup' => 'required|numeric|min:0',
                'pengeluaran_lain' => 'nullable|numeric|min:0',
                'sumber_dana' => 'nullable|string|max:100',
            ]);

            $hasil = $this->service->hitungMandiri($request->all(), $param);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'hasil' => $hasil,
            ]);
        }

        return redirect()->route('dashboard.simulasi.index', [
            'mode' => $mode,
            'mitra_id' => $request->mitra_id,
        ])->with('hasil_kalkulasi', $hasil)->withInput();
    }

    /**
     * Simpan Hasil Simulasi ke Riwayat Pengguna.
     */
    public function simpan(Request $request)
    {
        $user = Auth::user();
        $param = ParameterSimulasiFinansial::getActive();
        $mode = $request->input('mode', 'mandiri');

        $request->validate([
            'judul_simulasi' => 'required|string|max:150',
            'mode' => 'required|in:mandiri,mitra',
            'catatan_pengguna' => 'nullable|string|max:500',
            'biaya_keberangkatan' => 'required|numeric|min:1000000',
            'gaji_jpy' => 'required|integer|min:50000',
            'kurs_jpy' => 'required|numeric|min:1',
            'biaya_hidup' => 'required|numeric|min:0',
            'pengeluaran_lain' => 'nullable|numeric|min:0',
        ]);

        $inputData = $request->all();
        $mitra = null;
        $namaMitraSnapshot = null;

        if ($mode === 'mitra') {
            $request->validate([
                'mitra_id' => 'required|exists:mitra_pembiayaans,id',
                'tenor_bulan' => 'required|integer|min:1',
            ]);

            $mitra = MitraPembiayaan::findOrFail($request->mitra_id);
            $namaMitraSnapshot = $mitra->nama_mitra;
            $hasilKalkulasi = $this->service->hitungMitra($inputData, $mitra, $param);

            $snapshotParameter = [
                'kurs_jpy_idr' => (float) $param->kurs_jpy_idr,
                'sumber_informasi_param' => $param->sumber_informasi,
                'mitra_nama' => $mitra->nama_mitra,
                'mitra_skema' => $mitra->jenis_skema,
                'mitra_bunga_tahunan' => $mitra->suku_bunga_tahunan,
                'mitra_margin_bulanan' => $mitra->margin_bulanan,
                'mitra_metode' => $mitra->metode_perhitungan,
                'mitra_biaya_admin' => $mitra->biaya_administrasi_nilai,
                'mitra_biaya_admin_tipe' => $mitra->biaya_administrasi_tipe,
                'mitra_sumber' => $mitra->sumber_informasi,
                'mitra_tanggal_berlaku' => $mitra->tanggal_berlaku?->format('Y-m-d'),
            ];
        } else {
            $hasilKalkulasi = $this->service->hitungMandiri($inputData, $param);

            $snapshotParameter = [
                'kurs_jpy_idr' => (float) $param->kurs_jpy_idr,
                'sumber_informasi' => $param->sumber_informasi,
                'keterangan_asumsi' => $param->keterangan_asumsi,
            ];
        }

        $riwayat = RiwayatSimulasiFinansial::create([
            'user_id' => $user->id,
            'jenis_simulasi' => $mode,
            'judul_simulasi' => $request->judul_simulasi,
            'mitra_pembiayaan_id' => $mitra?->id,
            'nama_mitra_snapshot' => $namaMitraSnapshot,
            'snapshot_input' => $inputData,
            'snapshot_parameter' => $snapshotParameter,
            'snapshot_hasil' => $hasilKalkulasi,
            'catatan_pengguna' => $request->catatan_pengguna,
        ]);

        return redirect()->route('dashboard.simulasi.riwayat.show', $riwayat->id)
            ->with('success', 'Hasil simulasi finansial berhasil disimpan ke riwayat akun Anda!');
    }

    /**
     * Halaman Riwayat Simulasi Pengguna.
     */
    public function riwayat()
    {
        $user = Auth::user();
        $riwayats = $user->riwayatSimulasis()->paginate(10);

        return view('dashboard.simulasi.riwayat', compact('user', 'riwayats'));
    }

    /**
     * Detail Riwayat Simulasi (Dilindungi Hak Akses).
     */
    public function showRiwayat(RiwayatSimulasiFinansial $riwayat)
    {
        $user = Auth::user();

        // Validasi kepemilikan riwayat
        if ($riwayat->user_id !== $user->id && ! $user->isAdminSekolah()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk melihat riwayat simulasi pengguna lain.');
        }

        return view('dashboard.simulasi.detail-riwayat', compact('user', 'riwayat'));
    }

    /**
     * Hapus Riwayat Simulasi Milik Pengguna.
     */
    public function destroyRiwayat(RiwayatSimulasiFinansial $riwayat)
    {
        $user = Auth::user();

        // Validasi kepemilikan riwayat
        if ($riwayat->user_id !== $user->id && ! $user->isAdminSekolah()) {
            abort(403, 'Akses ditolak: Anda tidak dapat menghapus riwayat milik pengguna lain.');
        }

        $riwayat->delete();

        return redirect()->route('dashboard.simulasi.riwayat')
            ->with('success', 'Riwayat simulasi finansial berhasil dihapus.');
    }
}
