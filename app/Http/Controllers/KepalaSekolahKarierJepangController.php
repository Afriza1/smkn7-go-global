<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranKarierJepang;
use App\Models\RiwayatPendaftaranKarier;
use App\Services\SchoolMonitoringStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KepalaSekolahKarierJepangController extends Controller
{
    /**
     * Tampilan Dashboard Monitoring & Persetujuan Kepala Sekolah.
     */
    public function index()
    {
        $menungguPersetujuan = PendaftaranKarierJepang::with(['lpk', 'user', 'adminChecker', 'bkChecker'])
            ->where('status', 'menunggu_kepala_sekolah')
            ->whereHas('lpk', fn ($query) => $query->where('is_demo', false))
            ->latest()
            ->get();

        $bidangKerja2025 = [
            ['Care Giver', 5], ['Scaffolding', 3], ['Pengemasan/pengepakan industri', 1],
            ['Pengecoran logam', 1], ['Industri manufaktur', 1],
            ['Konstruksi (pemlesteran dinding dan lantai)', 1], ['Bengkel mobil', 1],
        ];
        $mitraResmi = ['KANZO Indonesia', 'Gakkou Nusantara'];
        // Retained school-confirmed executive figures already used by this dashboard.
        $sumberResmi = [
            'total_siswa' => 874,
            'alumni_aktif_jepang' => app(SchoolMonitoringStatistics::class)->alumniAktifJepang(),
            'peserta_pelatihan' => 16,
            'mitra_resmi' => count($mitraResmi),
        ];
        $ringkasanUtama = [
            ...$sumberResmi,
            'peserta_program_jepang' => PendaftaranKarierJepang::query()
                ->whereHas('lpk', fn ($query) => $query->where('is_demo', false))->distinct('user_id')->count('user_id'),
        ];
        $tracerStudyStats = $this->tracerStudyStats();
        $aktivitasTerkini = RiwayatPendaftaranKarier::query()
            ->with(['pendaftaran.lpk', 'user'])
            ->whereHas('pendaftaran.lpk', fn ($query) => $query->where('is_demo', false))
            ->whereIn('action', ['persetujuan_kepala_sekolah', 'penolakan_kepala_sekolah', 'update_status_lpk'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.kepala-sekolah', compact(
            'menungguPersetujuan', 'ringkasanUtama',
            'bidangKerja2025', 'mitraResmi', 'tracerStudyStats', 'aktivitasTerkini'
        ));
    }

    private function tracerStudyStats(): array
    {
        $base = DB::table('tracer_studies')->whereIn('record_type', ['historical', 'manual', 'website']);
        $historicalCount = (clone $base)->where('record_type', 'historical')->count();
        $accountResponseCount = (clone $base)->whereIn('record_type', ['manual', 'website'])
            ->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $total = $historicalCount + $accountResponseCount;

        $status = [];
        foreach ((clone $base)->where(function ($query): void {
            $query->whereNotNull('status_asli')->where('status_asli', '<>', '')
                ->orWhereNotNull('status_kegiatan')->where('status_kegiatan', '<>', '');
        })->selectRaw("COALESCE(NULLIF(status_asli, ''), NULLIF(status_kegiatan, '')) as label")->cursor() as $row) {
            $label = trim((string) $row->label);
            if ($label !== '') $status[$label] = ($status[$label] ?? 0) + 1;
        }

        $majors = [];
        foreach ((clone $base)->whereNotNull('jurusan')->where('jurusan', '<>', '')->pluck('jurusan') as $value) {
            $label = trim((string) $value);
            if ($label !== '') $majors[$label] = ($majors[$label] ?? 0) + 1;
        }

        $companies = [];
        foreach ((clone $base)->select(['record_type', 'instansi_asli', 'nama_perusahaan', 'nama_lengkap_terenkripsi'])->cursor() as $row) {
            $label = trim((string) ($row->instansi_asli ?: $row->nama_perusahaan));
            if ($label === '') continue;
            $personalName = null;
            if ($row->record_type === 'historical' && $row->nama_lengkap_terenkripsi) {
                try { $personalName = Crypt::decryptString($row->nama_lengkap_terenkripsi); } catch (\Throwable) {}
            }
            if ($personalName !== null && mb_strtolower(trim($personalName)) === mb_strtolower($label)) continue;
            $companies[$label] = ($companies[$label] ?? 0) + 1;
        }

        $positions = [];
        foreach ((clone $base)->where(function ($query): void {
            $query->whereNotNull('posisi_asli')->where('posisi_asli', '<>', '')
                ->orWhereNotNull('pekerjaan')->where('pekerjaan', '<>', '');
        })->selectRaw("COALESCE(NULLIF(posisi_asli, ''), NULLIF(pekerjaan, '')) as label")->cursor() as $row) {
            $label = trim((string) $row->label);
            if ($label !== '') $positions[$label] = ($positions[$label] ?? 0) + 1;
        }

        $placements = [];
        foreach ((clone $base)->where(function ($query): void {
            $query->whereNotNull('penempatan_asli')->where('penempatan_asli', '<>', '');
        })->selectRaw("NULLIF(penempatan_asli, '') as label")->cursor() as $row) {
            $label = trim((string) $row->label);
            if ($label !== '') $placements[$label] = ($placements[$label] ?? 0) + 1;
        }

        $sort = function (array $groups): array {
            uksort($groups, fn ($left, $right) => ($groups[$right] <=> $groups[$left]) ?: strnatcasecmp((string) $left, (string) $right));
            return collect($groups)->map(fn ($count, $label) => (object) ['label' => $label, 'count' => $count])->values()->all();
        };

        $years = (clone $base)->select('tahun_pencatatan as label')->selectRaw('COUNT(*) as count')
            ->groupBy('tahun_pencatatan')->orderBy('tahun_pencatatan')->get();

        return [
            'total' => $total,
            'years' => $years,
            'status_total' => array_sum($status),
            'status' => $sort($status),
            'majors' => $sort($majors),
            'companies' => $sort($companies),
            'positions' => $sort($positions),
            'placements' => $sort($placements),
        ];
    }

    /**
     * Halaman Detail Usulan Pendaftaran untuk Diputuskan Kepala Sekolah.
     */
    public function show(PendaftaranKarierJepang $pendaftaran)
    {
        $pendaftaran->load(['lpk', 'user', 'adminChecker', 'bkChecker', 'riwayats.user']);
        $trackerSteps = $pendaftaran->getTrackerSteps();

        return view('dashboard.kepala-sekolah.show', compact('pendaftaran', 'trackerSteps'));
    }

    /**
     * Eksekusi Putusan Kepala Sekolah (Setujui / Tolak).
     */
    public function putusan(Request $request, PendaftaranKarierJepang $pendaftaran)
    {
        $kepalaSekolah = Auth::user();

        $request->validate([
            'putusan' => 'required|in:setujui,tolak',
            'catatan' => 'required_if:putusan,tolak|nullable|string|max:1000',
        ], [
            'catatan.required_if' => 'Alasan keputusan penolakan wajib dicantumkan oleh pimpinan sekolah.',
        ]);

        $statusSebelumnya = $pendaftaran->status;

        if ($request->putusan === 'setujui') {
            $pendaftaran->status = 'disetujui_sekolah';
            $pendaftaran->catatan_kepala_sekolah = $request->catatan ?: 'Disetujui resmi oleh Kepala Sekolah untuk diteruskan ke proses seleksi dan pembinaan LPK.';
            $action = 'persetujuan_kepala_sekolah';
            $msg = 'Pengajuan peserta atas nama ' . $pendaftaran->nama_lengkap . ' BERHASIL DISETUJUI dan diteruskan ke program Karier Jepang.';
        } else {
            $pendaftaran->status = 'ditolak_kepala_sekolah';
            $pendaftaran->catatan_kepala_sekolah = $request->catatan;
            $action = 'penolakan_kepala_sekolah';
            $msg = 'Pengajuan peserta atas nama ' . $pendaftaran->nama_lengkap . ' TIDAK DISETUJUI dengan catatan keputusan resmi.';
        }

        $pendaftaran->kepala_sekolah_checked_by = $kepalaSekolah->id;
        $pendaftaran->kepala_sekolah_checked_at = now();
        $pendaftaran->save();

        RiwayatPendaftaranKarier::create([
            'pendaftaran_id' => $pendaftaran->id,
            'user_id' => $kepalaSekolah->id,
            'action' => $action,
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $pendaftaran->status,
            'catatan' => $pendaftaran->catatan_kepala_sekolah,
        ]);

        return redirect()->route('dashboard.kepala-sekolah')->with('success', $msg);
    }
}
