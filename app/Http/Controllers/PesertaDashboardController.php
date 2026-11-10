<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranKarierJepang;
use App\Models\UserAccountVerificationRequest;
use App\Services\StudentIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaDashboardController extends Controller
{
    /**
     * Dashboard Khusus Siswa Aktif.
     */
    public function siswa()
    {
        $user = Auth::user();

        if ($user->status !== 'aktif') {
            $verificationRequest = UserAccountVerificationRequest::query()
                ->where('user_id', $user->id)
                ->orderByDesc('submission_number')
                ->first();

            return view('dashboard.siswa.verifikasi-status', compact('user', 'verificationRequest'));
        }

        $pendaftaranAktif = PendaftaranKarierJepang::with(['lpk', 'adminChecker', 'bkChecker', 'kepalaSekolahChecker'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['ditolak_admin', 'ditolak_kepala_sekolah', 'tidak_lolos_seleksi', 'mengundurkan_diri', 'proses_dihentikan'])
            ->latest()
            ->first();

        $riwayatPendaftaran = PendaftaranKarierJepang::with('lpk')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $trackerSteps = $pendaftaranAktif ? $pendaftaranAktif->getTrackerSteps() : [];

        $nisnStored = $user->student_identity_type === 'nisn' && filled($user->student_identity_encrypted);
        $parentIdentity = [
            'stored' => $nisnStored,
            'match_ready' => $nisnStored && filled($user->student_identity_hash),
            'nisn' => $nisnStored
                ? app(StudentIdentity::class)->decryptNisn($user->student_identity_encrypted, $user->student_identity_type)
                : null,
        ];
        return view('dashboard.siswa.index', compact('user', 'pendaftaranAktif', 'riwayatPendaftaran', 'trackerSteps', 'parentIdentity'));
    }

    /**
     * Dashboard Khusus Alumni.
     */
    public function alumni()
    {
        $user = Auth::user();

        $pendaftaranAktif = PendaftaranKarierJepang::with(['lpk', 'adminChecker', 'bkChecker', 'kepalaSekolahChecker'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['ditolak_admin', 'ditolak_kepala_sekolah', 'tidak_lolos_seleksi', 'mengundurkan_diri', 'proses_dihentikan'])
            ->latest()
            ->first();

        $riwayatPendaftaran = PendaftaranKarierJepang::with('lpk')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $trackerSteps = $pendaftaranAktif ? $pendaftaranAktif->getTrackerSteps() : [];

        return view('dashboard.alumni.index', compact('user', 'pendaftaranAktif', 'riwayatPendaftaran', 'trackerSteps'));
    }
}
