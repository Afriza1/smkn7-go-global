<?php

namespace App\Http\Controllers;

use App\Models\Lpk;
use App\Models\PendaftaranKarierJepang;
use App\Models\RiwayatPendaftaranKarier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminKarierJepangController extends Controller
{
    /**
     * Dashboard Pengelolaan Karier Jepang bagi Admin Sekolah.
     */
    public function index(Request $request)
    {
        $query = PendaftaranKarierJepang::with(['lpk', 'user']);

        // Search filter
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('nis_nisn', 'like', "%{$q}%")
                    ->orWhere('nomor_pendaftaran', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // LPK filter
        if ($request->filled('lpk_id') && $request->lpk_id !== 'semua') {
            $query->where('lpk_id', $request->lpk_id);
        }

        $pendaftarans = $query->latest()->paginate(10)->withQueryString();

        // Macro counters
        $stats = [
            'total' => PendaftaranKarierJepang::count(),
            'menunggu_admin' => PendaftaranKarierJepang::where('status', 'menunggu_admin')->count(),
            'perlu_perbaikan' => PendaftaranKarierJepang::where('status', 'perlu_perbaikan')->count(),
            'menunggu_bk' => PendaftaranKarierJepang::where('status', 'menunggu_bk')->count(),
            'menunggu_ks' => PendaftaranKarierJepang::where('status', 'menunggu_kepala_sekolah')->count(),
            'disetujui' => PendaftaranKarierJepang::whereIn('status', [
                'disetujui_sekolah', 'menunggu_seleksi_lpk', 'seleksi_berlangsung', 'lolos_seleksi',
                'pelatihan', 'mcu', 'pengurusan_dokumen', 'persiapan_keberangkatan', 'diberangkatkan'
            ])->count(),
            'ditolak' => PendaftaranKarierJepang::whereIn('status', ['ditolak_admin', 'ditolak_kepala_sekolah'])->count(),
        ];

        $lpks = Lpk::aktif()->get();

        return view('dashboard.admin.index', compact('pendaftarans', 'stats', 'lpks'));
    }

    /**
     * Detail Pemeriksaan & Tindakan Admin.
     */
    public function show(PendaftaranKarierJepang $pendaftaran)
    {
        $pendaftaran->load(['lpk', 'user', 'riwayats.user', 'adminChecker', 'bkChecker', 'kepalaSekolahChecker']);
        $trackerSteps = $pendaftaran->getTrackerSteps();

        return view('dashboard.admin.show', compact('pendaftaran', 'trackerSteps'));
    }

    /**
     * Eksekusi Tindakan Verifikasi Administrasi oleh Admin.
     */
    public function verifikasi(Request $request, PendaftaranKarierJepang $pendaftaran)
    {
        $admin = Auth::user();

        $request->validate([
            'tindakan' => 'required|in:lolos,perbaikan,tolak',
            'catatan' => 'required_if:tindakan,perbaikan,tolak|nullable|string|max:1000',
        ], [
            'catatan.required_if' => 'Catatan wajib diisi apabila mengembalikan berkas atau menolak pendaftaran.',
        ]);

        $statusSebelumnya = $pendaftaran->status;

        if ($request->tindakan === 'lolos') {
            $pendaftaran->status = 'menunggu_bk';
            $pendaftaran->catatan_admin = $request->catatan ?: 'Berkas administrasi dan identitas dinyatakan lengkap dan memenuhi syarat.';
            $pendaftaran->admin_checked_by = $admin->id;
            $pendaftaran->admin_checked_at = now();
            $action = 'verifikasi_admin_lolos';
            $msg = 'Pemeriksaan administrasi disetujui. Pengajuan diteruskan kepada Guru BK untuk pertimbangan kesiapan.';
        } elseif ($request->tindakan === 'perbaikan') {
            $pendaftaran->status = 'perlu_perbaikan';
            $pendaftaran->catatan_admin = $request->catatan;
            $pendaftaran->admin_checked_by = $admin->id;
            $pendaftaran->admin_checked_at = now();
            $action = 'verifikasi_admin_revisi';
            $msg = 'Berkas dikembalikan kepada peserta untuk perbaikan dengan catatan resmi terlampir.';
        } else {
            $pendaftaran->status = 'ditolak_admin';
            $pendaftaran->catatan_admin = $request->catatan;
            $pendaftaran->admin_checked_by = $admin->id;
            $pendaftaran->admin_checked_at = now();
            $action = 'verifikasi_admin_tolak';
            $msg = 'Pendaftaran ditolak pada tahap administrasi dengan alasan tercatat.';
        }

        $pendaftaran->save();

        RiwayatPendaftaranKarier::create([
            'pendaftaran_id' => $pendaftaran->id,
            'user_id' => $admin->id,
            'action' => $action,
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $pendaftaran->status,
            'catatan' => $pendaftaran->catatan_admin,
        ]);

        return redirect()->route('dashboard.admin.show', $pendaftaran->id)->with('success', $msg);
    }

    /**
     * Pembaruan Status Perkembangan LPK oleh Admin Sekolah.
     */
    public function updateStatusLpk(Request $request, PendaftaranKarierJepang $pendaftaran)
    {
        $admin = Auth::user();

        $request->validate([
            'status_lpk' => 'required|in:menunggu_seleksi_lpk,seleksi_berlangsung,lolos_seleksi,tidak_lolos_seleksi,pelatihan,mcu,pengurusan_dokumen,persiapan_keberangkatan,diberangkatkan,mengundurkan_diri,proses_dihentikan',
            'catatan_lpk' => 'nullable|string|max:1000',
        ]);

        $statusSebelumnya = $pendaftaran->status;
        $pendaftaran->status = $request->status_lpk;
        $pendaftaran->catatan_lpk_admin = $request->catatan_lpk;
        $pendaftaran->lpk_updated_at = now();
        $pendaftaran->save();

        RiwayatPendaftaranKarier::create([
            'pendaftaran_id' => $pendaftaran->id,
            'user_id' => $admin->id,
            'action' => 'update_status_lpk',
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $pendaftaran->status,
            'catatan' => 'Pembaruan progres LPK: ' . $pendaftaran->getStatusLabel() . ($request->catatan_lpk ? ' — ' . $request->catatan_lpk : ''),
        ]);

        return redirect()->route('dashboard.admin.show', $pendaftaran->id)
            ->with('success', 'Status perkembangan peserta di LPK berhasil diperbarui menjadi: ' . $pendaftaran->getStatusLabel());
    }
}
