<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranKarierJepang;
use App\Models\RiwayatPendaftaranKarier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruBkKarierJepangController extends Controller
{
    /**
     * Dashboard Guru BK / Pembimbing Karier.
     */
    public function index()
    {
        $menungguPertimbangan = PendaftaranKarierJepang::with(['lpk', 'user', 'adminChecker'])
            ->where('status', 'menunggu_bk')
            ->latest()
            ->get();

        $riwayatPertimbangan = PendaftaranKarierJepang::with(['lpk', 'user'])
            ->whereNotNull('bk_checked_at')
            ->latest('bk_checked_at')
            ->take(10)
            ->get();

        return view('dashboard.guru.index', compact('menungguPertimbangan', 'riwayatPertimbangan'));
    }

    /**
     * Detail & Form Pertimbangan Guru BK.
     */
    public function show(PendaftaranKarierJepang $pendaftaran)
    {
        $pendaftaran->load(['lpk', 'user', 'adminChecker', 'riwayats.user']);
        $trackerSteps = $pendaftaran->getTrackerSteps();

        return view('dashboard.guru.show', compact('pendaftaran', 'trackerSteps'));
    }

    /**
     * Kirim Pertimbangan BK dan Teruskan ke Kepala Sekolah.
     */
    public function submitPertimbangan(Request $request, PendaftaranKarierJepang $pendaftaran)
    {
        $guru = Auth::user();

        $request->validate([
            'catatan_bk' => 'required|string|min:10|max:1000',
        ], [
            'catatan_bk.required' => 'Catatan pertimbangan kesiapan siswa wajib diisi sebelum meneruskan ke Kepala Sekolah.',
            'catatan_bk.min' => 'Catatan pertimbangan minimal 10 karakter.',
        ]);

        $statusSebelumnya = $pendaftaran->status;
        $pendaftaran->status = 'menunggu_kepala_sekolah';
        $pendaftaran->catatan_bk = $request->catatan_bk;
        $pendaftaran->bk_checked_by = $guru->id;
        $pendaftaran->bk_checked_at = now();
        $pendaftaran->save();

        RiwayatPendaftaranKarier::create([
            'pendaftaran_id' => $pendaftaran->id,
            'user_id' => $guru->id,
            'action' => 'pertimbangan_bk',
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => 'menunggu_kepala_sekolah',
            'catatan' => $request->catatan_bk,
        ]);

        return redirect()->route('dashboard.guru')
            ->with('success', 'Pertimbangan BK berhasil dicatat! Berkas pendaftaran peserta telah diteruskan ke meja Kepala Sekolah.');
    }
}
