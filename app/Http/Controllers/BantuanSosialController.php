<?php

namespace App\Http\Controllers;

use App\Models\DokumenPengajuanBantuan;
use App\Models\PengajuanBantuan;
use App\Models\ProgramBantuan;
use App\Models\RiwayatStatusBantuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BantuanSosialController extends Controller
{
    /**
     * Halaman Utama Kolaborasi & Bantuan Sosial (Siswa & Alumni).
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Program Bantuan Aktif yang Dapat Diajukan
        $programs = ProgramBantuan::aktif()
            ->orderBy('id', 'asc')
            ->get();

        // 2. Riwayat Pengajuan Bantuan Milik Pengguna yang Sedang Login
        $pengajuans = PengajuanBantuan::where('user_id', $user->id)
            ->with(['programBantuan', 'dokumens'])
            ->latest('id')
            ->get();

        return view('dashboard.bantuan.index', compact('programs', 'pengajuans', 'user'));
    }

    /**
     * Detail Informasi Program Bantuan & Persyaratannya.
     */
    public function showProgram(ProgramBantuan $program)
    {
        return view('dashboard.bantuan.program-detail', compact('program'));
    }

    /**
     * Formulir Pengajuan Permohonan Bantuan.
     */
    public function create(ProgramBantuan $program)
    {
        if (! $program->isBuka()) {
            return redirect()->route('dashboard.bantuan.index')
                ->with('error', 'Program bantuan yang Anda pilih saat ini sedang tidak menerima permohonan baru.');
        }

        $user = Auth::user();

        // Cek apakah ada pengajuan aktif yang belum selesai/ditutup untuk program ini
        $pengajuanAktif = PengajuanBantuan::where('user_id', $user->id)
            ->where('program_bantuan_id', $program->id)
            ->whereIn('status', [
                PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN,
                PengajuanBantuan::STATUS_PERLU_PERBAIKAN,
                PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH,
                PengajuanBantuan::STATUS_DITERUSKAN_MITRA,
                PengajuanBantuan::STATUS_SEDANG_DIPROSES_MITRA,
            ])
            ->first();

        if ($pengajuanAktif) {
            return redirect()->route('dashboard.bantuan.show', $pengajuanAktif->id)
                ->with('info', "Anda telah memiliki pengajuan aktif ({$pengajuanAktif->nomor_pengajuan}) untuk program ini yang sedang dalam proses verifikasi.");
        }

        return view('dashboard.bantuan.create', compact('program', 'user'));
    }

    /**
     * Simpan Pengajuan Permohonan Bantuan & Unggah Berkas Persyaratan (Private Storage).
     */
    public function store(Request $request, ProgramBantuan $program)
    {
        if (! $program->isBuka()) {
            return redirect()->route('dashboard.bantuan.index')
                ->with('error', 'Program bantuan tidak sedang dibuka.');
        }

        $user = Auth::user();

        $rules = [
            'alasan_pengajuan' => 'required|string|min:15|max:2000',
            'informasi_tambahan' => 'nullable|string|max:1000',
            'persetujuan_data' => 'accepted',
        ];

        // Validasi file dokumen yang diwajibkan sesuai spesifikasi program
        $dokumenList = $program->dokumen_dibutuhkan ?? [];
        foreach ($dokumenList as $index => $namaDok) {
            $fieldName = 'dokumen_' . $index;
            $rules[$fieldName] = 'required|file|mimes:pdf,jpg,jpeg,png|max:3072'; // Max 3MB
        }

        $validated = $request->validate($rules, [
            'alasan_pengajuan.required' => 'Tuliskan alasan pengajuan permohonan bantuan secara jelas.',
            'alasan_pengajuan.min' => 'Alasan pengajuan minimal berisi 15 karakter.',
            'persetujuan_data.accepted' => 'Anda wajib menyetujui pemrosesan dan verifikasi data oleh sekolah dan mitra.',
        ]);

        return DB::transaction(function () use ($request, $program, $user, $validated, $dokumenList) {
            // Generate nomor pengajuan unik
            $bulanTahun = now()->format('Ym');
            $countThisMonth = PengajuanBantuan::where('nomor_pengajuan', 'like', "BANSOS-{$bulanTahun}-%")->count() + 1;
            $nomorPengajuan = sprintf('BANSOS-%s-%04d', $bulanTahun, $countThisMonth);

            $pengajuan = PengajuanBantuan::create([
                'nomor_pengajuan' => $nomorPengajuan,
                'program_bantuan_id' => $program->id,
                'user_id' => $user->id,
                'alasan_pengajuan' => $validated['alasan_pengajuan'],
                'informasi_tambahan' => $validated['informasi_tambahan'] ?? null,
                'status' => PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN,
                'keterangan_pemohon' => 'Permohonan bantuan Anda telah terdaftar dan sedang dalam antrean pemeriksaan berkas oleh Admin BKK SMKN 7 Jember.',
                'persetujuan_data' => true,
            ]);

            // Unggah berkas ke penyimpanan privat (storage/app/private/dokumen_bantuan/{id})
            $storageFolder = 'dokumen_bantuan/' . $pengajuan->id;
            foreach ($dokumenList as $index => $namaDok) {
                $fieldName = 'dokumen_' . $index;
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $path = $file->store($storageFolder, 'local');

                    DokumenPengajuanBantuan::create([
                        'pengajuan_bantuan_id' => $pengajuan->id,
                        'nama_dokumen' => $namaDok,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ]);
                }
            }

            // Catat ke audit trail status
            RiwayatStatusBantuan::create([
                'pengajuan_bantuan_id' => $pengajuan->id,
                'user_id' => $user->id,
                'status_sebelumnya' => null,
                'status_baru' => PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN,
                'keterangan' => 'Pengajuan permohonan berhasil dikirimkan oleh pemohon bersama ' . count($dokumenList) . ' berkas persyaratan.',
            ]);

            return redirect()->route('dashboard.bantuan.show', $pengajuan->id)
                ->with('success', "Permohonan bantuan ({$nomorPengajuan}) berhasil diajukan! Pantau pembaruan status secara berkala di halaman ini.");
        });
    }

    /**
     * Detail Status Pengajuan & Kronologi Proses (Hanya Pemilik).
     */
    public function show(PengajuanBantuan $pengajuan)
    {
        $user = Auth::user();

        // Strict Resource Ownership Guard
        if ($pengajuan->user_id !== $user->id && ! $user->isAdminSekolah()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki hak untuk melihat permohonan bantuan ini.');
        }

        $pengajuan->load(['programBantuan', 'dokumens', 'riwayatStatuses.user']);

        return view('dashboard.bantuan.show', compact('pengajuan', 'user'));
    }

    /**
     * Form Perbaikan Dokumen untuk Siswa / Alumni.
     */
    public function formPerbaikan(PengajuanBantuan $pengajuan)
    {
        $user = Auth::user();

        if ($pengajuan->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $pengajuan->canBeFixed()) {
            return redirect()->route('dashboard.bantuan.show', $pengajuan->id)
                ->with('error', 'Status pengajuan saat ini tidak membutuhkan perbaikan berkas.');
        }

        $pengajuan->load(['programBantuan', 'dokumens']);

        return view('dashboard.bantuan.perbaikan', compact('pengajuan', 'user'));
    }

    /**
     * Simpan Perbaikan Dokumen.
     */
    public function storePerbaikan(Request $request, PengajuanBantuan $pengajuan)
    {
        $user = Auth::user();

        if ($pengajuan->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $pengajuan->canBeFixed()) {
            return redirect()->route('dashboard.bantuan.show', $pengajuan->id)
                ->with('error', 'Pengajuan tidak dapat diperbaiki pada status saat ini.');
        }

        $request->validate([
            'catatan_pemohon' => 'nullable|string|max:1000',
            'dokumen_perbaikan.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        $updatedCount = 0;
        $storageFolder = 'dokumen_bantuan/' . $pengajuan->id;

        if ($request->hasFile('dokumen_perbaikan')) {
            foreach ($request->file('dokumen_perbaikan') as $dokumenId => $file) {
                $docRecord = DokumenPengajuanBantuan::where('pengajuan_bantuan_id', $pengajuan->id)
                    ->where('id', $dokumenId)
                    ->first();

                if ($docRecord && $file) {
                    // Hapus file lama jika ada
                    if (Storage::disk('local')->exists($docRecord->file_path)) {
                        Storage::disk('local')->delete($docRecord->file_path);
                    }

                    $newPath = $file->store($storageFolder, 'local');
                    $docRecord->update([
                        'file_path' => $newPath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ]);
                    $updatedCount++;
                }
            }
        }

        $statusLama = $pengajuan->status;
        $pengajuan->update([
            'status' => PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN,
            'keterangan_pemohon' => 'Perbaikan berkas telah dikirimkan oleh pemohon. Menunggu pemeriksaan ulang oleh Admin BKK.',
            'catatan_perbaikan' => null,
        ]);

        RiwayatStatusBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN,
            'keterangan' => "Pemohon telah mengunggah perbaikan {$updatedCount} berkas. " . ($request->catatan_pemohon ? "Catatan: {$request->catatan_pemohon}" : ''),
        ]);

        return redirect()->route('dashboard.bantuan.show', $pengajuan->id)
            ->with('success', 'Berkas perbaikan berhasil dikirimkan ke sekolah untuk diverifikasi ulang.');
    }

    /**
     * Batalkan Permohonan Bantuan oleh Siswa / Alumni.
     */
    public function batal(Request $request, PengajuanBantuan $pengajuan)
    {
        $user = Auth::user();

        if ($pengajuan->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $pengajuan->canBeCancelled()) {
            return redirect()->route('dashboard.bantuan.show', $pengajuan->id)
                ->with('error', 'Permohonan tidak dapat dibatalkan pada status proses saat ini.');
        }

        $request->validate([
            'alasan_batal' => 'required|string|min:5|max:500',
        ]);

        $statusLama = $pengajuan->status;
        $pengajuan->update([
            'status' => PengajuanBantuan::STATUS_DIBATALKAN,
            'keterangan_pemohon' => 'Permohonan telah dibatalkan secara mandiri oleh pemohon dengan alasan: ' . $request->alasan_batal,
        ]);

        RiwayatStatusBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => PengajuanBantuan::STATUS_DIBATALKAN,
            'keterangan' => 'Permohonan dibatalkan oleh siswa/alumni: ' . $request->alasan_batal,
        ]);

        return redirect()->route('dashboard.bantuan.show', $pengajuan->id)
            ->with('success', 'Permohonan bantuan berhasil dibatalkan.');
    }

    /**
     * Unduh Berkas Persyaratan secara Privat (Hanya Pemilik & Admin BKK).
     */
    public function unduhDokumen(DokumenPengajuanBantuan $dokumen)
    {
        $user = Auth::user();
        $pengajuan = $dokumen->pengajuanBantuan;

        if (! $pengajuan) {
            abort(404, 'Data pengajuan tidak ditemukan.');
        }

        // Keamanan: Hanya pemilik berkas atau Admin Sekolah yang boleh mengunduh
        if ($pengajuan->user_id !== $user->id && ! $user->isAdminSekolah()) {
            abort(403, 'Akses berkas ditolak: Anda tidak memiliki wewenang untuk melihat dokumen privat ini.');
        }

        if (! Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404, 'File berkas tidak ditemukan di penyimpanan server privat.');
        }

        return Storage::disk('local')->response(
            $dokumen->file_path,
            $dokumen->file_name
        );
    }
}
