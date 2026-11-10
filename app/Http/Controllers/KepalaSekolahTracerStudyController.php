<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KepalaSekolahTracerStudyController extends Controller
{
    private const ALUMNI_TYPES = ['historical', 'manual', 'website'];

    public function index(Request $request)
    {
        $base = DB::table('tracer_studies')->whereIn('record_type', self::ALUMNI_TYPES);
        $years = (clone $base)->whereNotNull('tahun_pencatatan')->distinct()->orderByDesc('tahun_pencatatan')->pluck('tahun_pencatatan');
        $filters = $request->validate([
            'tahun_pencatatan' => ['nullable', Rule::in(array_merge(['all'], $years->map(fn ($year) => (string) $year)->all()))],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', Rule::in(['10', '25', '50', '100'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $selectedYear = (string) ($filters['tahun_pencatatan'] ?? 'all');
        $selectedMajor = trim((string) ($filters['jurusan'] ?? ''));
        $selectedStatus = trim((string) ($filters['status'] ?? ''));
        $search = trim((string) ($filters['q'] ?? ''));
        $perPage = (int) ($filters['per_page'] ?? 10);

        $filtered = clone $base;
        if ($selectedYear !== 'all') {
            $filtered->where('tahun_pencatatan', (int) $selectedYear);
        }
        if ($selectedMajor !== '') {
            $rawMajors = $this->rawMajorsForDisplay($selectedMajor);
            $filtered->whereIn('jurusan', $rawMajors ?: [$selectedMajor]);
        }
        if ($selectedStatus !== '') {
            if (mb_strtolower($selectedStatus) === 'bekerja') {
                $filtered->whereRaw("LOWER(COALESCE(NULLIF(status_asli, ''), NULLIF(status_kegiatan, ''))) = ?", ['bekerja']);
            } else {
                $filtered->whereRaw("COALESCE(NULLIF(status_asli, ''), NULLIF(status_kegiatan, '')) = ?", [$selectedStatus]);
            }
        }

        if ($search !== '') {
            $ids = [];
            foreach ((clone $filtered)->orderBy('tahun_pencatatan')->orderBy('id')->cursor() as $candidate) {
                if ($this->matchesSearch($candidate, $search)) {
                    $ids[] = (int) $candidate->id;
                }
            }
            $totalFiltered = count($ids);
            $page = LengthAwarePaginator::resolveCurrentPage();
            $pageIds = array_slice($ids, ($page - 1) * $perPage, $perPage);
            $pageRows = $pageIds === [] ? collect() : DB::table('tracer_studies')->whereIn('id', $pageIds)->get()->sortBy(fn ($row) => array_search((int) $row->id, $pageIds, true))->values();
            $records = new LengthAwarePaginator($pageRows, $totalFiltered, $perPage, $page, [
                'path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query(),
            ]);
        } else {
            $records = $filtered->orderBy('tahun_pencatatan')->orderBy('id')->paginate($perPage)->withQueryString();
        }
        $records->fragment('data-alumni-tracer-study');
        $records->getCollection()->transform(fn ($record) => $this->presentRecord($record));

        if ($request->ajax()) {
            $programOptions = $this->normalizeMajors(
                $this->distinctValues($base, "NULLIF(jurusan, '')", 'jurusan')
            )->pluck('jurusan')->filter()->values();
            $statusOptions = $this->mergeCaseVariants(
                $this->distinctValues($base, "COALESCE(NULLIF(status_asli, ''), NULLIF(status_kegiatan, ''))", 'status_label'),
                ['bekerja' => 'Bekerja'], 'status_label'
            )->pluck('status_label')->filter()->values();

            return response()->view('dashboard.kepala-sekolah.partials.tracer-study-table', [
                'years' => $years, 'selectedYear' => $selectedYear,
                'programOptions' => $programOptions, 'statusOptions' => $statusOptions,
                'selectedMajor' => $selectedMajor, 'selectedStatus' => $selectedStatus,
                'search' => $search, 'perPage' => $perPage, 'records' => $records,
                'tableRoute' => $request->routeIs('dashboard.guru.tracer-study.statistics')
                    ? route('dashboard.guru.tracer-study.statistics')
                    : route('dashboard.kepala-sekolah.tracer-study'),
            ]);
        }

        $yearTotals = (clone $base)->select('tahun_pencatatan as tahun')
            ->selectRaw('COUNT(*) as total_records')->groupBy('tahun_pencatatan')->orderBy('tahun_pencatatan')->get();
        $totalRecords = (int) $yearTotals->sum('total_records');
        $statusGroups = $this->mergeCaseVariants(
            $this->aggregate($base, "COALESCE(NULLIF(status_asli, ''), NULLIF(status_kegiatan, ''))", 'status_label'),
            ['bekerja' => 'Bekerja'], 'status_label'
        );
        $majorGroups = $this->normalizeMajors($this->aggregate($base, "NULLIF(jurusan, '')", 'jurusan', true));
        $companyGroups = $this->aggregate($base, "COALESCE(NULLIF(instansi_asli, ''), NULLIF(nama_perusahaan, ''))", 'label');
        $positionGroups = $this->mergeCaseVariants(
            $this->aggregate($base, "COALESCE(NULLIF(posisi_asli, ''), NULLIF(pekerjaan, ''))", 'label'),
            ['mekanik' => 'Mekanik']
        );
        $placementGroups = $this->mergeCaseVariants(
            $this->aggregate($base, "COALESCE(NULLIF(penempatan_asli, ''), NULLIF(alamat_tempat_kerja, ''))", 'label'),
            ['kerja' => 'Kerja']
        );
        $programOptions = $majorGroups->pluck('jurusan')->filter()->values();
        $statusOptions = $statusGroups->pluck('status_label')->filter()->values();
        $progressGroups = $yearTotals->map(fn ($row) => (object) ['label' => (string) $row->tahun, 'total_records' => (int) $row->total_records]);
        $tableRoute = $request->routeIs('dashboard.guru.tracer-study.statistics')
            ? route('dashboard.guru.tracer-study.statistics')
            : route('dashboard.kepala-sekolah.tracer-study');

        return view('dashboard.kepala-sekolah.tracer-study', [
            'years' => $years, 'selectedYear' => $selectedYear, 'yearTotals' => $yearTotals,
            'totalRecords' => $totalRecords, 'progressGroups' => $progressGroups,
            'statusGroups' => $statusGroups, 'majorGroups' => $majorGroups,
            'companyGroups' => $companyGroups, 'positionGroups' => $positionGroups,
            'placementGroups' => $placementGroups, 'programOptions' => $programOptions,
            'statusOptions' => $statusOptions, 'selectedMajor' => $selectedMajor,
            'selectedStatus' => $selectedStatus, 'search' => $search,
            'perPage' => $perPage, 'records' => $records,
            'tableRoute' => $tableRoute,
        ]);
    }

    private function aggregate(Builder $base, string $expression, string $alias, bool $sortByLabel = false): Collection
    {
        $counts = [];
        foreach ((clone $base)->whereRaw("{$expression} IS NOT NULL")
            ->selectRaw("{$expression} as {$alias}")->cursor() as $row) {
            $label = trim((string) $row->{$alias});
            if ($label !== '') {
                $counts[$label] = ($counts[$label] ?? 0) + 1;
            }
        }
        $groups = collect($counts)->map(fn ($count, $label) => (object) [$alias => $label, 'total_records' => $count]);

        if ($sortByLabel) {
            return $groups->sort(fn ($left, $right) => strnatcasecmp($left->{$alias}, $right->{$alias}))->values();
        }

        return $groups->sort(fn ($left, $right) => ($right->total_records <=> $left->total_records)
            ?: strnatcasecmp($left->{$alias}, $right->{$alias}))->values();
    }

    private function distinctValues(Builder $base, string $expression, string $alias): Collection
    {
        $values = [];
        foreach ((clone $base)->whereRaw("{$expression} IS NOT NULL")
            ->selectRaw("{$expression} as {$alias}")->cursor() as $row) {
            $value = trim((string) $row->{$alias});
            if ($value !== '') {
                $values[$value] = 1;
            }
        }

        return collect($values)->map(fn ($count, $value) => (object) [$alias => $value, 'total_records' => $count])->values();
    }

    private function normalizeMajors(Collection $groups): Collection
    {
        $map = [
            'tkj' => 'Teknik Komputer dan Jaringan (TKJ)',
            'teknik komputer dan jaringan' => 'Teknik Komputer dan Jaringan (TKJ)',
            'tkr' => 'Teknik Kendaraan Ringan (TKR)',
            'teknik kendaraan ringan' => 'Teknik Kendaraan Ringan (TKR)',
            'tsm' => 'Teknik Sepeda Motor (TSM)',
            'teknik sepeda motor' => 'Teknik Sepeda Motor (TSM)',
            'dkv' => 'Desain Komunikasi Visual (DKV)',
            'desain komunikasi visual' => 'Desain Komunikasi Visual (DKV)',
            'kkr' => 'KKR',
        ];
        $normalized = [];
        foreach ($groups as $group) {
            $key = mb_strtolower(trim($group->jurusan));
            $label = $map[$key] ?? $group->jurusan;
            $normalized[$label] = ($normalized[$label] ?? 0) + $group->total_records;
        }

        return collect($normalized)->map(fn ($count, $label) => (object) ['jurusan' => $label, 'total_records' => $count])
            ->sortBy('jurusan', SORT_NATURAL | SORT_FLAG_CASE)->values();
    }

    private function rawMajorsForDisplay(string $label): array
    {
        $raw = [
            'Teknik Komputer dan Jaringan (TKJ)' => ['TKJ', 'Teknik Komputer dan Jaringan', 'Teknik Komputer Dan Jaringan'],
            'Teknik Kendaraan Ringan (TKR)' => ['TKR', 'Teknik Kendaraan Ringan'],
            'Teknik Sepeda Motor (TSM)' => ['TSM', 'Teknik Sepeda Motor'],
            'Desain Komunikasi Visual (DKV)' => ['DKV', 'Desain Komunikasi Visual'],
            'KKR' => ['KKR'],
        ];

        return $raw[$label] ?? [$label];
    }

    private function mergeCaseVariants(Collection $groups, array $labels, string $alias = 'label'): Collection
    {
        $merged = [];
        foreach ($groups as $group) {
            $key = mb_strtolower($group->{$alias});
            $label = $labels[$key] ?? $group->{$alias};
            $merged[$label] = ($merged[$label] ?? 0) + $group->total_records;
        }

        return collect($merged)->map(fn ($count, $label) => (object) [$alias => $label, 'total_records' => $count])
            ->sortByDesc('total_records')->values();
    }

    private function matchesSearch(object $record, string $search): bool
    {
        $payload = $this->sourcePayload($record);
        $haystack = [
            $this->recordName($record),
            $this->payloadValue($payload, ['kabupaten/kota', 'kabupaten', 'kota']),
            $this->payloadValue($payload, ['nama instansi', 'pt / perusahaan', 'pt/perusahaan', 'nama perusahaan']),
            $record->instansi_asli ?? null, $record->nama_perusahaan ?? null,
        ];
        $needle = mb_strtolower($search);
        foreach ($haystack as $value) {
            if (is_string($value) && str_contains(mb_strtolower($value), $needle)) {
                return true;
            }
        }

        return false;
    }

    private function presentRecord(object $record): object
    {
        $payload = $this->sourcePayload($record);
        $status = $record->status_asli ?: $record->status_kegiatan;

        return (object) [
            'id' => (int) $record->id,
            'nomor' => $record->source_row_number ?: $record->id,
            'tahun' => $record->tahun_pencatatan,
            'kabupaten' => $this->payloadValue($payload, ['kabupaten/kota', 'kabupaten', 'kota']) ?? '—',
            'jenjang' => $this->payloadValue($payload, ['jenjang']) ?? '—',
            'sekolah' => $this->payloadValue($payload, ['nama sekolah', 'sekolah']) ?? '—',
            'nama' => $this->recordName($record) ?? '—',
            'jurusan' => $record->jurusan ?: '—',
            'status' => $status ?: '—',
            'instansi' => $record->instansi_asli ?: $record->nama_perusahaan ?: '—',
            'posisi' => $record->posisi_asli ?: $record->pekerjaan ?: '—',
            'penempatan' => $record->penempatan_asli ?: (($record->record_type === 'website') ? ($record->alamat_tempat_kerja ?: null) : null) ?: '—',
            'record_type' => $record->record_type,
        ];
    }

    private function recordName(object $record): ?string
    {
        if ($record->record_type === 'historical' && ! empty($record->nama_lengkap_terenkripsi)) {
            try {
                return Crypt::decryptString($record->nama_lengkap_terenkripsi);
            } catch (\Throwable) {
                return null;
            }
        }

        return $record->nama_lengkap ?? null;
    }

    private function sourcePayload(object $record): array
    {
        if (empty($record->data_sumber)) {
            return [];
        }
        try {
            $payload = json_decode(Crypt::decryptString($record->data_sumber), true, 512, JSON_THROW_ON_ERROR);
            return is_array($payload) ? $payload : [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function payloadValue(array $payload, array $headers): ?string
    {
        $accepted = array_map(fn ($header) => mb_strtolower(trim($header)), $headers);
        foreach ($payload as $field) {
            if (! is_array($field) || ! isset($field['header'])) {
                continue;
            }
            if (in_array(mb_strtolower(trim((string) $field['header'])), $accepted, true)) {
                $value = trim((string) ($field['value'] ?? ''));
                return $value !== '' ? $value : null;
            }
        }

        return null;
    }
}
