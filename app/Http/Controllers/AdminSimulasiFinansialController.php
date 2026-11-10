<?php

namespace App\Http\Controllers;

use App\Models\MitraPembiayaan;
use App\Models\ParameterSimulasiFinansial;
use App\Models\RiwayatSimulasiFinansial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminSimulasiFinansialController extends Controller
{
    /**
     * Dashboard Pengelolaan Simulasi Finansial bagi Admin.
     */
    public function index()
    {
        $param = ParameterSimulasiFinansial::getActive();
        $mitras = MitraPembiayaan::orderBy('is_aktif', 'desc')->orderBy('nama_mitra')->get();

        // Statistik agregat
        $stats = [
            'total_simulasi' => RiwayatSimulasiFinansial::count(),
            'total_mandiri' => RiwayatSimulasiFinansial::where('jenis_simulasi', 'mandiri')->count(),
            'total_mitra' => RiwayatSimulasiFinansial::where('jenis_simulasi', 'mitra')->count(),
            'total_pengguna_unik' => RiwayatSimulasiFinansial::distinct('user_id')->count('user_id'),
            'total_mitra_aktif' => MitraPembiayaan::where('is_aktif', true)->count(),
        ];

        // Aktivitas agregat terbaru (tanpa membuka identitas pribadi secara berlebihan)
        $aktivitasTerbaru = RiwayatSimulasiFinansial::with(['user', 'mitraPembiayaan'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.admin.simulasi.index', compact('param', 'mitras', 'stats', 'aktivitasTerbaru'));
    }

    /**
     * Perbarui Parameter Global Simulasi.
     */
    public function updateParameter(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'kurs_jpy_idr' => 'required|numeric|min:1',
            'default_gaji_jpy' => 'required|integer|min:50000',
            'default_biaya_keberangkatan' => 'required|numeric|min:1000000',
            'default_biaya_hidup_jepang' => 'required|numeric|min:0',
            'default_potongan_lainnya' => 'required|numeric|min:0',
            'sumber_informasi' => 'required|string|max:255',
            'keterangan_asumsi' => 'nullable|string|max:1000',
        ]);

        $param = ParameterSimulasiFinansial::getActive();
        $param->update([
            ...$validated,
            'tanggal_pembaruan' => now(),
            'updated_by' => $admin->id,
        ]);

        return redirect()->route('dashboard.admin.simulasi.index')
            ->with('success', 'Parameter umum simulasi finansial berhasil diperbarui.');
    }

    /**
     * Tambah Mitra Pembiayaan Baru.
     */
    public function storeMitra(Request $request)
    {
        $validated = $request->validate([
            'nama_mitra' => 'required|string|max:150',
            'jenis_skema' => 'required|string|max:150',
            'plafon_maksimal' => 'required|numeric|min:1000000',
            'suku_bunga_tahunan' => 'nullable|numeric|min:0|max:100',
            'margin_bulanan' => 'nullable|numeric|min:0|max:100',
            'metode_perhitungan' => 'required|in:flat,anuitas,margin_syariah,belum_tersedia',
            'tenor_pilihan' => 'required|array',
            'tenor_pilihan.*' => 'integer|min:1|max:60',
            'biaya_administrasi_tipe' => 'required|in:nominal,persentase',
            'biaya_administrasi_nilai' => 'required|numeric|min:0',
            'uang_muka_persen' => 'required|numeric|min:0|max:100',
            'metode_pembayaran' => 'nullable|string|max:150',
            'persyaratan' => 'nullable|string',
            'sumber_informasi' => 'nullable|string|max:255',
            'tanggal_berlaku' => 'nullable|date',
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
            'keterangan_status' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['nama_mitra']);
        $countSlug = MitraPembiayaan::where('slug', 'like', "{$slug}%")->count();
        if ($countSlug > 0) {
            $slug .= '-' . ($countSlug + 1);
        }

        MitraPembiayaan::create([
            ...$validated,
            'slug' => $slug,
            'is_aktif' => $request->boolean('is_aktif', true),
            'is_demo' => $request->boolean('is_demo', false),
        ]);

        return redirect()->route('dashboard.admin.simulasi.index')
            ->with('success', 'Mitra pembiayaan baru berhasil ditambahkan.');
    }

    /**
     * Perbarui Data Mitra Pembiayaan.
     */
    public function updateMitra(Request $request, MitraPembiayaan $mitra)
    {
        $validated = $request->validate([
            'nama_mitra' => 'required|string|max:150',
            'jenis_skema' => 'required|string|max:150',
            'plafon_maksimal' => 'required|numeric|min:1000000',
            'suku_bunga_tahunan' => 'nullable|numeric|min:0|max:100',
            'margin_bulanan' => 'nullable|numeric|min:0|max:100',
            'metode_perhitungan' => 'required|in:flat,anuitas,margin_syariah,belum_tersedia',
            'tenor_pilihan' => 'required|array',
            'tenor_pilihan.*' => 'integer|min:1|max:60',
            'biaya_administrasi_tipe' => 'required|in:nominal,persentase',
            'biaya_administrasi_nilai' => 'required|numeric|min:0',
            'uang_muka_persen' => 'required|numeric|min:0|max:100',
            'metode_pembayaran' => 'nullable|string|max:150',
            'persyaratan' => 'nullable|string',
            'sumber_informasi' => 'nullable|string|max:255',
            'tanggal_berlaku' => 'nullable|date',
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
            'keterangan_status' => 'nullable|string|max:500',
        ]);

        $mitra->update([
            ...$validated,
            'is_aktif' => $request->boolean('is_aktif', true),
            'is_demo' => $request->boolean('is_demo', false),
        ]);

        return redirect()->route('dashboard.admin.simulasi.index')
            ->with('success', 'Data mitra pembiayaan berhasil diperbarui.');
    }

    /**
     * Aktifkan / Nonaktifkan Mitra Pembiayaan.
     */
    public function toggleMitra(MitraPembiayaan $mitra)
    {
        $mitra->is_aktif = ! $mitra->is_aktif;
        $mitra->save();

        $statusText = $mitra->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('dashboard.admin.simulasi.index')
            ->with('success', "Mitra pembiayaan {$mitra->nama_mitra} berhasil {$statusText}.");
    }
}
