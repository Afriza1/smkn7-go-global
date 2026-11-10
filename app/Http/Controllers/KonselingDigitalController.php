<?php

namespace App\Http\Controllers;

use App\Models\KonselingDigital;
use App\Models\KontakLayananKonseling;
use App\Models\RiwayatStatusKonseling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KonselingDigitalController extends Controller
{
    /**
     * Dashboard & Daftar Konseling Siswa/Alumni.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $kontak = KontakLayananKonseling::getAktif();

        $statusFilter = $request->query('status');
        $query = $user->konselingDigitals()->with('guru');

        if ($statusFilter && in_array($statusFilter, [
            KonselingDigital::STATUS_MENUNGGU_KONFIRMASI,
            KonselingDigital::STATUS_DIJADWALKAN,
            KonselingDigital::STATUS_BERLANGSUNG,
            KonselingDigital::STATUS_SELESAI,
            KonselingDigital::STATUS_DITOLAK,
            KonselingDigital::STATUS_DIBATALKAN,
        ])) {
            $query->where('status', $statusFilter);
        }

        $konselings = $query->paginate(8)->withQueryString();

        // Statistik ringkas pengguna
        $stats = [
            'total' => $user->konselingDigitals()->count(),
            'berlangsung' => $user->konselingDigitals()->where('status', KonselingDigital::STATUS_BERLANGSUNG)->count(),
            'dijadwalkan' => $user->konselingDigitals()->where('status', KonselingDigital::STATUS_DIJADWALKAN)->count(),
            'menunggu' => $user->konselingDigitals()->where('status', KonselingDigital::STATUS_MENUNGGU_KONFIRMASI)->count(),
            'selesai' => $user->konselingDigitals()->where('status', KonselingDigital::STATUS_SELESAI)->count(),
        ];

        // Sesi aktif yang sedang berlangsung (prioritas utama)
        $sesiAktif = $user->konselingDigitals()
            ->where('status', KonselingDigital::STATUS_BERLANGSUNG)
            ->first();

        return view('dashboard.konseling.index', compact('user', 'kontak', 'konselings', 'stats', 'sesiAktif', 'statusFilter'));
    }

    /**
     * Tampilan Formulir Pengajuan Konsultasi Baru.
     */
    public function create()
    {
        $user = Auth::user();
        $kontak = KontakLayananKonseling::getAktif();
        $kategoriList = KonselingDigital::getKategoriList();

        return view('dashboard.konseling.create', compact('user', 'kontak', 'kategoriList'));
    }

    /**
     * Simpan Pengajuan Konsultasi Baru ke Database.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'kategori' => 'required|in:' . implode(',', array_keys(KonselingDigital::getKategoriList())),
            'metode' => 'required|in:online,tatap_muka',
            'alasan_konseling' => 'required|string|min:10|max:1500',
            'tanggal_usulan' => 'required|date|after_or_equal:today',
            'waktu_usulan' => 'required|string|max:50',
        ], [
            'kategori.required' => 'Pilih salah satu kategori bimbingan konseling.',
            'metode.required' => 'Pilih metode pelaksanaan (online website atau tatap muka di sekolah).',
            'alasan_konseling.required' => 'Tuliskan alasan atau hal yang ingin Anda konsultasikan secara singkat.',
            'alasan_konseling.min' => 'Alasan konsultasi minimal 10 karakter.',
            'tanggal_usulan.required' => 'Tentukan usulan tanggal konsultasi.',
            'tanggal_usulan.after_or_equal' => 'Usulan tanggal tidak boleh di masa lampau.',
            'waktu_usulan.required' => 'Tentukan perkiraan waktu/jam yang diinginkan.',
        ]);

        $nomorKonseling = KonselingDigital::generateNomorKonseling();

        $konseling = KonselingDigital::create([
            'nomor_konseling' => $nomorKonseling,
            'user_id' => $user->id,
            'guru_id' => null, // Belum ditentukan oleh Guru BK
            'kategori' => $validated['kategori'],
            'metode' => $validated['metode'],
            'alasan_konseling' => $validated['alasan_konseling'],
            'tanggal_usulan' => $validated['tanggal_usulan'],
            'waktu_usulan' => $validated['waktu_usulan'],
            'status' => KonselingDigital::STATUS_MENUNGGU_KONFIRMASI,
        ]);

        // Catat di log riwayat status
        RiwayatStatusKonseling::create([
            'konseling_id' => $konseling->id,
            'user_id' => $user->id,
            'status_sebelumnya' => null,
            'status_baru' => KonselingDigital::STATUS_MENUNGGU_KONFIRMASI,
            'keterangan' => 'Pengajuan konsultasi berhasil dikirim oleh ' . $user->name,
        ]);

        return redirect()->route('dashboard.konseling.show', $konseling->id)
            ->with('success', 'Pengajuan konseling berhasil dikirim! Guru BK akan memeriksa dan menentukan jadwal resmi.');
    }

    /**
     * Detail Status & Jadwal Konsultasi.
     */
    public function show(KonselingDigital $konseling)
    {
        $user = Auth::user();

        // Otorisasi: hanya pemilik yang dapat melihat
        if ($konseling->user_id !== $user->id) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk melihat pengajuan pengguna lain.');
        }

        $konseling->load(['guru', 'riwayatStatuses.user']);
        $kontak = KontakLayananKonseling::getAktif();

        return view('dashboard.konseling.show', compact('user', 'konseling', 'kontak'));
    }

    /**
     * Batalkan Pengajuan (hanya jika masih menunggu konfirmasi).
     */
    public function batal(Request $request, KonselingDigital $konseling)
    {
        $user = Auth::user();

        if ($konseling->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $konseling->bisaBatal()) {
            return back()->with('error', 'Konsultasi yang telah dijadwalkan atau berlangsung tidak dapat dibatalkan secara sepihak.');
        }

        $request->validate([
            'alasan_pembatalan' => 'nullable|string|max:500',
        ]);

        $statusLama = $konseling->status;
        $konseling->update([
            'status' => KonselingDigital::STATUS_DIBATALKAN,
            'alasan_pembatalan' => $request->input('alasan_pembatalan', 'Dibatalkan oleh pemohon.'),
        ]);

        RiwayatStatusKonseling::create([
            'konseling_id' => $konseling->id,
            'user_id' => $user->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => KonselingDigital::STATUS_DIBATALKAN,
            'keterangan' => $request->input('alasan_pembatalan', 'Dibatalkan oleh pemohon.'),
        ]);

        return redirect()->route('dashboard.konseling.index')
            ->with('success', 'Pengajuan konsultasi berhasil dibatalkan.');
    }

    /**
     * Ruang Chat Konsultasi Digital (Online).
     */
    public function chat(KonselingDigital $konseling)
    {
        $user = Auth::user();

        if ($konseling->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $konseling->isOnline()) {
            return redirect()->route('dashboard.konseling.show', $konseling->id)
                ->with('error', 'Konsultasi ini menggunakan metode tatap muka di sekolah, tidak melalui ruang chat online.');
        }

        return view('dashboard.konseling.chat', compact('user', 'konseling'));
    }
}
