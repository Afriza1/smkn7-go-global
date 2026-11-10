<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranKarierJepang;
use App\Services\ProgramReportExcelExport;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KepalaSekolahProgramReportController extends Controller
{
    private const IN_PROGRESS_STATUSES = [
        'menunggu_admin', 'perlu_perbaikan', 'menunggu_bk', 'menunggu_kepala_sekolah',
        'disetujui_sekolah', 'menunggu_seleksi_lpk', 'seleksi_berlangsung', 'lolos_seleksi',
        'pelatihan', 'mcu', 'pengurusan_dokumen', 'persiapan_keberangkatan',
    ];

    private const CLOSED_BEFORE_DEPARTURE_STATUSES = [
        'ditolak_admin', 'ditolak_kepala_sekolah', 'tidak_lolos_seleksi', 'mengundurkan_diri', 'proses_dihentikan',
    ];

    private const KNOWN_STATUSES = [
        'menunggu_admin', 'perlu_perbaikan', 'ditolak_admin', 'menunggu_bk',
        'menunggu_kepala_sekolah', 'ditolak_kepala_sekolah', 'disetujui_sekolah',
        'menunggu_seleksi_lpk', 'seleksi_berlangsung', 'lolos_seleksi', 'tidak_lolos_seleksi',
        'pelatihan', 'mcu', 'pengurusan_dokumen', 'persiapan_keberangkatan', 'diberangkatkan',
        'mengundurkan_diri', 'proses_dihentikan',
    ];

    public function show(string $report)
    {
        $reportData = $this->buildReport($report);
        $excelAvailable = class_exists(\ZipArchive::class);

        return view('dashboard.kepala-sekolah.reports.show', compact('reportData', 'excelAvailable'));
    }

    public function exportExcel(string $report, ProgramReportExcelExport $exporter): BinaryFileResponse
    {
        abort_unless(class_exists(\ZipArchive::class), 503, 'Ekspor Excel memerlukan ekstensi PHP ZipArchive.');

        $reportData = $this->buildReport($report);
        $sections = array_merge([[
            'title' => 'Ringkasan laporan',
            'headers' => ['Indikator', 'Nilai'],
            'rows' => $reportData['summary'],
        ]], $reportData['sections']);
        $file = $exporter->create($reportData['title'], $sections);

        return response()->download($file, $reportData['filename'] . '.xlsx')->deleteFileAfterSend(true);
    }

    private function buildReport(string $report): array
    {
        return match ($report) {
            'perkembangan' => $this->progressReport(),
            'keberangkatan-penempatan' => $this->departureReport(),
            'kemitraan-mou' => $this->partnershipReport(),
            default => abort(404),
        };
    }

    private function actualApplications()
    {
        return PendaftaranKarierJepang::query()
            ->whereHas('lpk', fn ($query) => $query->where('is_demo', false));
    }

    private function progressReport(): array
    {
        $applications = $this->actualApplications();
        $total = (clone $applications)->count();
        $successful = (clone $applications)->where('status', 'diberangkatkan')->count();
        $inProgress = (clone $applications)->whereIn('status', self::IN_PROGRESS_STATUSES)->count();
        $closedBeforeDeparture = (clone $applications)->whereIn('status', self::CLOSED_BEFORE_DEPARTURE_STATUSES)->count();

        $statusCounts = (clone $applications)
            ->select('status')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $statusRows = collect(self::KNOWN_STATUSES)
            ->merge($statusCounts->keys())
            ->unique()
            ->map(function (string $status) use ($statusCounts): array {
                $statusModel = new PendaftaranKarierJepang;
                $statusModel->status = $status;

                return [$statusModel->getStatusLabel(), (int) ($statusCounts[$status] ?? 0)];
            })
            ->all();

        $applicationYears = (clone $applications)->get(['created_at'])
            ->groupBy(fn (PendaftaranKarierJepang $application) => $application->created_at?->format('Y') ?? 'Tidak tercatat')
            ->map(fn (Collection $records, string $year): array => [$year, $records->count()])
            ->sortKeysDesc()
            ->values()
            ->all();

        return [
            'slug' => 'perkembangan',
            'filename' => 'laporan-perkembangan-peserta',
            'title' => 'Laporan Perkembangan Peserta',
            'description' => 'Rekap pendaftaran berdasarkan status tahapan program saat ini. Tahun menggunakan tahun pendaftaran dari created_at.',
            'notice' => 'Satu peserta dihitung menurut status pendaftarannya saat ini. Riwayat perpindahan status tersedia di sistem, tetapi laporan ini bukan hitungan kumulatif peserta yang pernah melewati setiap tahap.',
            'summary' => [
                ['Total pendaftaran aktual', $total],
                ['Berhasil diberangkatkan', $successful],
                ['Masih dalam tahapan program', $inProgress],
                ['Proses berakhir sebelum keberangkatan', $closedBeforeDeparture],
            ],
            'sections' => [
                ['title' => 'Peserta menurut status saat ini', 'headers' => ['Tahap / Status', 'Jumlah peserta'], 'rows' => $statusRows],
                ['title' => 'Pendaftaran menurut tahun', 'headers' => ['Tahun pendaftaran', 'Jumlah pendaftaran'], 'rows' => $applicationYears],
            ],
        ];
    }

    private function departureReport(): array
    {
        $departed = $this->actualApplications()
            ->with('lpk:id,nama_lpk,wilayah_penempatan,bidang_program')
            ->where('jenis_peserta', 'alumni')
            ->where('status', 'diberangkatkan')
            ->get(['id', 'user_id', 'lpk_id', 'created_at']);

        $byYear = $departed
            ->groupBy(fn (PendaftaranKarierJepang $application) => $application->created_at?->format('Y') ?? 'Tidak tercatat')
            ->map(fn (Collection $records, string $year): array => [$year, $records->unique('user_id')->count()])
            ->sortKeysDesc()
            ->values()
            ->all();

        $byPartner = $departed
            ->groupBy(fn (PendaftaranKarierJepang $application) => ($application->created_at?->format('Y') ?? 'Tidak tercatat') . '|' . $application->lpk_id)
            ->map(function (Collection $records): array {
                $application = $records->first();
                $lpk = $application->lpk;

                return [
                    $application->created_at?->format('Y') ?? 'Tidak tercatat',
                    $lpk?->nama_lpk ?? 'Tidak tercatat',
                    $lpk?->wilayah_penempatan ?: 'Tidak tercatat',
                    $lpk?->bidang_program ?: 'Tidak tercatat',
                    $records->unique('user_id')->count(),
                ];
            })
            ->values()
            ->all();

        return [
            'slug' => 'keberangkatan-penempatan',
            'filename' => 'laporan-keberangkatan-penempatan',
            'title' => 'Laporan Keberangkatan & Penempatan',
            'description' => 'Rekap pendaftaran berstatus Diberangkatkan ke Jepang. Pengelompokan tahun berdasarkan tahun pendaftaran, bukan tanggal keberangkatan.',
            'notice' => 'Database tidak menyimpan tanggal keberangkatan, perusahaan penerima, atau pekerjaan aktual per pendaftaran. Wilayah dan bidang di bawah berasal dari profil LPK terkait, bukan konfirmasi penempatan aktual.',
            'summary' => [
                ['Alumni berstatus diberangkatkan', $departed->unique('user_id')->count()],
                ['Negara tujuan menurut status program', $departed->isNotEmpty() ? 'Jepang' : 'Tidak ada data keberangkatan'],
            ],
            'sections' => [
                ['title' => 'Keberangkatan menurut tahun pendaftaran', 'headers' => ['Tahun pendaftaran', 'Jumlah berstatus diberangkatkan'], 'rows' => $byYear],
                ['title' => 'LPK dan informasi pada profil mitra', 'headers' => ['Tahun pendaftaran', 'Nama LPK', 'Wilayah pada profil LPK', 'Bidang program LPK', 'Jumlah peserta'], 'rows' => $byPartner],
            ],
        ];
    }

    private function partnershipReport(): array
    {
        // Official school partnerships are confirmed by the school, independent of LPK records.
        $partners = ['KANZO Indonesia', 'Gakkou Nusantara'];
        $rows = array_map(fn (string $partner): array => [$partner, 'Mitra resmi sekolah'], $partners);

        return [
            'slug' => 'kemitraan-mou',
            'filename' => 'laporan-kemitraan-mou-industri',
            'title' => 'Daftar Mitra Resmi Sekolah',
            'description' => 'Daftar mitra resmi sekolah berdasarkan konfirmasi sekolah.',
            'notice' => 'Sumber: konfirmasi sekolah. Data ini bukan hasil query database LPK. Informasi MoU, masa berlaku, kuota, dan perusahaan penerima belum tersedia.',
            'summary' => [
                ['Mitra resmi sekolah', count($partners)],
            ],
            'sections' => [[
                'title' => 'Mitra resmi sekolah',
                'headers' => ['Nama mitra', 'Status kemitraan'],
                'rows' => $rows,
            ]],
        ];
    }
}
