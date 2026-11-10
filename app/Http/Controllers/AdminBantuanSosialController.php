<?php

namespace App\Http\Controllers;

use App\Models\CatatanInternalBantuan;
use App\Models\PengajuanBantuan;
use App\Models\ProgramBantuan;
use App\Models\RiwayatStatusBantuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminBantuanSosialController extends Controller
{
    /**
     * Halaman Dashboard Manajemen Kolaborasi & Bantuan Sosial (Admin BKK).
     */
    public function index(Request $request)
    {
        $query = PengajuanBantuan::with(['user', 'programBantuan', 'dokumens']);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Program
        if ($request->filled('program_id')) {
            $query->where('program_bantuan_id', $request->program_id);
        }

        // Pencarian Nama Siswa / Nomor Registrasi / NISN
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $pengajuans = $query->latest('id')->paginate(15)->withQueryString();

        // Data KPI
        $stats = [
            'total' => PengajuanBantuan::count(),
            'menunggu' => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN)->count(),
            'perbaikan' => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_PERLU_PERBAIKAN)->count(),
            'terverifikasi' => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH)->count(),
            'diteruskan' => PengajuanBantuan::whereIn('status', [
                PengajuanBantuan::STATUS_DITERUSKAN_MITRA,
                PengajuanBantuan::STATUS_SEDANG_DIPROSES_MITRA,
            ])->count(),
            'disetujui' => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_DISETUJUI)->count(),
            'selesai' => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_SELESAI)->count(),
            'ditolak' => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_DITOLAK)->count(),
        ];

        // Daftar Program Bantuan untuk Tab Kelola Program
        $programs = ProgramBantuan::withCount('pengajuans')->orderBy('id', 'desc')->get();

        return view('dashboard.admin.bantuan.index', compact(
            'pengajuans',
            'programs',
            'stats'
        ));
    }

    /**
     * Halaman Detail Pemeriksaan & Tindak Lanjut Pengajuan Bantuan oleh Admin BKK.
     */
    public function show(PengajuanBantuan $pengajuan)
    {
        $pengajuan->load([
            'user',
            'programBantuan',
            'dokumens',
            'catatanInternals.admin',
            'riwayatStatuses.user',
            'diverifikasiOleh',
        ]);

        return view('dashboard.admin.bantuan.show', compact('pengajuan'));
    }

    /**
     * Verifikasi Kelayakan Berkas Tingkat Sekolah.
     */
    public function verifikasi(Request $request, PengajuanBantuan $pengajuan)
    {
        $admin = Auth::user();

        $statusLama = $pengajuan->status;
        $keterangan = $request->input('keterangan', 'Seluruh berkas persyaratan telah diverifikasi lengkap dan dinyatakan memenuhi kriteria rekomendasi sekolah.');

        $pengajuan->update([
            'status' => PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH,
            'keterangan_pemohon' => $keterangan,
            'diverifikasi_oleh' => $admin->id,
            'diverifikasi_pada' => now(),
            'catatan_perbaikan' => null,
        ]);

        RiwayatStatusBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH,
            'keterangan' => 'Verifikasi sekolah disetujui oleh Admin BKK: ' . $keterangan,
        ]);

        return back()->with('success', "Pengajuan {$pengajuan->nomor_pengajuan} berhasil diverifikasi oleh sekolah.");
    }

    /**
     * Minta Perbaikan Dokumen kepada Pemohon.
     */
    public function mintaPerbaikan(Request $request, PengajuanBantuan $pengajuan)
    {
        $admin = Auth::user();

        $request->validate([
            'catatan_perbaikan' => 'required|string|min:5|max:1500',
        ], [
            'catatan_perbaikan.required' => 'Tuliskan catatan perbaikan berkas secara spesifik agar pemohon dapat memperbaikinya.',
        ]);

        $statusLama = $pengajuan->status;
        $pengajuan->update([
            'status' => PengajuanBantuan::STATUS_PERLU_PERBAIKAN,
            'catatan_perbaikan' => $request->catatan_perbaikan,
            'keterangan_pemohon' => 'Terdapat berkas persyaratan yang perlu diperbaiki: ' . $request->catatan_perbaikan,
        ]);

        RiwayatStatusBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => PengajuanBantuan::STATUS_PERLU_PERBAIKAN,
            'keterangan' => 'Admin BKK meminta perbaikan berkas: ' . $request->catatan_perbaikan,
        ]);

        return back()->with('success', "Permintaan perbaikan berkas telah dikirimkan kepada pemohon.");
    }

    /**
     * Teruskan Berkas ke Mitra Kemitraan (Koordinasi Luar Sistem).
     */
    public function teruskanMitra(Request $request, PengajuanBantuan $pengajuan)
    {
        $admin = Auth::user();

        $request->validate([
            'nama_kontak_mitra' => 'nullable|string|max:255',
            'keterangan_terusan' => 'nullable|string|max:1000',
        ]);

        $statusLama = $pengajuan->status;
        $keteranganPemohon = $request->keterangan_terusan 
            ?: "Berkas permohonan Anda telah resmi diteruskan dan diserahkan kepada pihak {$pengajuan->programBantuan->nama_mitra} untuk proses penelaahan mitra.";

        $pengajuan->update([
            'status' => PengajuanBantuan::STATUS_DITERUSKAN_MITRA,
            'diteruskan_pada' => now(),
            'keterangan_pemohon' => $keteranganPemohon,
        ]);

        RiwayatStatusBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => PengajuanBantuan::STATUS_DITERUSKAN_MITRA,
            'keterangan' => "Berkas diteruskan ke mitra ({$pengajuan->programBantuan->nama_mitra})" . ($request->nama_kontak_mitra ? " via {$request->nama_kontak_mitra}" : ''),
        ]);

        return back()->with('success', "Status pengajuan diperbarui: Diteruskan ke {$pengajuan->programBantuan->nama_mitra}.");
    }

    /**
     * Update Perkembangan Mitra (Sedang Diproses).
     */
    public function updateProsesMitra(Request $request, PengajuanBantuan $pengajuan)
    {
        $admin = Auth::user();

        $request->validate([
            'keterangan' => 'required|string|max:1000',
        ]);

        $statusLama = $pengajuan->status;
        $pengajuan->update([
            'status' => PengajuanBantuan::STATUS_SEDANG_DIPROSES_MITRA,
            'keterangan_pemohon' => $request->keterangan,
        ]);

        RiwayatStatusBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => PengajuanBantuan::STATUS_SEDANG_DIPROSES_MITRA,
            'keterangan' => 'Perkembangan mitra: ' . $request->keterangan,
        ]);

        return back()->with('success', 'Perkembangan proses telaah mitra berhasil diperbarui.');
    }

    /**
     * Keputusan Akhir Mitra (Disetujui / Ditolak / Selesai).
     */
    public function putusanMitra(Request $request, PengajuanBantuan $pengajuan)
    {
        $admin = Auth::user();

        $request->validate([
            'status_putusan' => 'required|in:disetujui,ditolak,selesai',
            'keterangan' => 'required|string|min:5|max:1500',
        ]);

        $statusLama = $pengajuan->status;
        $statusBaru = match ($request->status_putusan) {
            'disetujui' => PengajuanBantuan::STATUS_DISETUJUI,
            'ditolak' => PengajuanBantuan::STATUS_DITOLAK,
            'selesai' => PengajuanBantuan::STATUS_SELESAI,
        };

        $pengajuan->update([
            'status' => $statusBaru,
            'keterangan_pemohon' => $request->keterangan,
            'diputuskan_pada' => now(),
        ]);

        $labelStatus = match ($statusBaru) {
            PengajuanBantuan::STATUS_DISETUJUI => 'DISETUJUI oleh Mitra',
            PengajuanBantuan::STATUS_DITOLAK => 'DITOLAK',
            PengajuanBantuan::STATUS_SELESAI => 'SELESAI / Tersalurkan',
            default => $statusBaru,
        };

        RiwayatStatusBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => $statusBaru,
            'keterangan' => "Keputusan: {$labelStatus}. Keterangan: {$request->keterangan}",
        ]);

        return back()->with('success', "Keputusan ({$labelStatus}) berhasil disimpan dan disampaikan kepada pemohon.");
    }

    /**
     * Simpan Catatan Internal Admin BKK (Privat, Tidak Pernah Tampil ke Pemohon).
     */
    public function simpanCatatanInternal(Request $request, PengajuanBantuan $pengajuan)
    {
        $admin = Auth::user();

        $request->validate([
            'tanggal_koordinasi' => 'required|date',
            'pihak_mitra' => 'required|string|max:255',
            'catatan_koordinasi' => 'required|string|min:5|max:3000',
            'keputusan_mitra' => 'nullable|string|max:255',
        ]);

        CatatanInternalBantuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'admin_id' => $admin->id,
            'tanggal_koordinasi' => $request->tanggal_koordinasi,
            'pihak_mitra' => $request->pihak_mitra,
            'catatan_koordinasi' => $request->catatan_koordinasi,
            'keputusan_mitra' => $request->keputusan_mitra,
        ]);

        return back()->with('success', 'Catatan internal koordinasi mitra berhasil disimpan secara privat.');
    }

    /**
     * Simpan Program Bantuan Baru.
     */
    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'nama_mitra' => 'required|string|max:255',
            'jenis_program' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:2000',
            'persyaratan' => 'required|string|max:3000',
            'dokumen_dibutuhkan_raw' => 'required|string',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
            'keterangan_status' => 'nullable|string|max:500',
        ]);

        $dokumenList = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->dokumen_dibutuhkan_raw)))));

        $slug = Str::slug($validated['nama_program']);
        if (ProgramBantuan::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        ProgramBantuan::create([
            'nama_program' => $validated['nama_program'],
            'slug' => $slug,
            'nama_mitra' => $validated['nama_mitra'],
            'jenis_program' => $validated['jenis_program'],
            'deskripsi' => $validated['deskripsi'],
            'persyaratan' => $validated['persyaratan'],
            'dokumen_dibutuhkan' => $dokumenList,
            'periode_mulai' => $validated['periode_mulai'] ?? null,
            'periode_selesai' => $validated['periode_selesai'] ?? null,
            'is_aktif' => $request->boolean('is_aktif'),
            'is_demo' => $request->boolean('is_demo'),
            'keterangan_status' => $validated['keterangan_status'] ?? null,
        ]);

        return redirect()->route('dashboard.admin.bantuan.index')
            ->with('success', "Program bantuan {$validated['nama_program']} berhasil dibuat.");
    }

    /**
     * Perbarui Program Bantuan.
     */
    public function updateProgram(Request $request, ProgramBantuan $program)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'nama_mitra' => 'required|string|max:255',
            'jenis_program' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:2000',
            'persyaratan' => 'required|string|max:3000',
            'dokumen_dibutuhkan_raw' => 'required|string',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date',
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
            'keterangan_status' => 'nullable|string|max:500',
        ]);

        $dokumenList = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->dokumen_dibutuhkan_raw)))));

        $program->update([
            'nama_program' => $validated['nama_program'],
            'nama_mitra' => $validated['nama_mitra'],
            'jenis_program' => $validated['jenis_program'],
            'deskripsi' => $validated['deskripsi'],
            'persyaratan' => $validated['persyaratan'],
            'dokumen_dibutuhkan' => $dokumenList,
            'periode_mulai' => $validated['periode_mulai'] ?? null,
            'periode_selesai' => $validated['periode_selesai'] ?? null,
            'is_aktif' => $request->boolean('is_aktif'),
            'is_demo' => $request->boolean('is_demo'),
            'keterangan_status' => $validated['keterangan_status'] ?? null,
        ]);

        return redirect()->route('dashboard.admin.bantuan.index')
            ->with('success', "Program bantuan {$program->nama_program} berhasil diperbarui.");
    }

    /**
     * Toggle Keaktifan Program.
     */
    public function toggleProgram(ProgramBantuan $program)
    {
        $program->is_aktif = ! $program->is_aktif;
        $program->save();

        $status = $program->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Program {$program->nama_program} berhasil {$status}.");
    }
}
