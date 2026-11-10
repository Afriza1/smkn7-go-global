<?php

namespace App\Http\Controllers;

use App\Models\TracerStudy;
use App\Models\TracerStudyPeriode;
use App\Services\TracerStudyExcelExport;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AdminTracerStudyController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request, false);
        $periods = TracerStudyPeriode::query()->orderByDesc('tahun')->get();
        $selectedPeriod = isset($filters['periode_id'])
            ? $periods->firstWhere('id', (int) $filters['periode_id'])
            : ($periods->firstWhere('status', TracerStudyPeriode::STATUS_OPEN) ?? $periods->first());

        $baseQuery = $selectedPeriod
            ? TracerStudy::query()->where('periode_id', $selectedPeriod->id)
            : TracerStudy::query()->whereRaw('1 = 0');

        $stats = [
            'total_respons' => (clone $baseQuery)->count(),
            'alumni_unik' => (clone $baseQuery)->distinct()->count('user_id'),
            'bekerja' => (clone $baseQuery)->where('status_kegiatan', 'bekerja')->count(),
            'magang' => (clone $baseQuery)->where('status_kegiatan', 'magang')->count(),
            'belum_diverifikasi' => (clone $baseQuery)->where('status_verifikasi', TracerStudy::STATUS_UNVERIFIED)->count(),
            'perlu_pembaruan' => (clone $baseQuery)->where('status_verifikasi', TracerStudy::STATUS_NEEDS_UPDATE)->count(),
        ];

        $rekapTahunLulus = (clone $baseQuery)
            ->select('tahun_lulus')
            ->selectRaw('COUNT(*) as total_respons')
            ->selectRaw('COUNT(DISTINCT user_id) as alumni_unik')
            ->groupBy('tahun_lulus')
            ->orderByDesc('tahun_lulus')
            ->get();

        $rekapJurusan = (clone $baseQuery)
            ->select('jurusan')
            ->selectRaw('COUNT(*) as total_respons')
            ->selectRaw('COUNT(DISTINCT user_id) as alumni_unik')
            ->groupBy('jurusan')
            ->orderBy('jurusan')
            ->get();

        $responses = $selectedPeriod
            ? $this->responseQuery($request, $selectedPeriod->id)
                ->with(['user:id,name,email', 'periode:id,tahun,nama'])
                ->latest()
                ->paginate(20)
                ->withQueryString()
            : TracerStudy::query()->whereRaw('1 = 0')->paginate(20);

        return view('dashboard.admin.tracer-study.index', compact(
            'periods', 'selectedPeriod', 'stats', 'rekapTahunLulus', 'rekapJurusan', 'responses', 'filters'
        ));
    }

    public function storePeriod(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'nullable|string|max:120',
            'keterangan' => 'nullable|string|max:5000',
            'tahun' => ['required', 'integer', 'min:1950', 'max:' . (now()->year + 1), Rule::unique('tracer_studies', 'tahun')->where(fn ($query) => $query->where('record_type', 'period'))],
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        try {
            TracerStudyPeriode::create([
                ...$validated,
                'nama' => filled($validated['nama'] ?? null) ? trim($validated['nama']) : null,
                'keterangan' => filled($validated['keterangan'] ?? null) ? trim($validated['keterangan']) : null,
                'status' => TracerStudyPeriode::STATUS_DRAFT,
                'active_key' => null,
                'dibuat_oleh' => Auth::id(),
            ]);
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                throw ValidationException::withMessages([
                    'tahun' => 'Periode untuk tahun tersebut sudah dibuat.',
                ]);
            }

            throw $exception;
        }

        $destination = Auth::user()?->isGuru()
            ? 'dashboard.guru.tracer-study.index'
            : 'dashboard.admin.tracer-study.index';

        return redirect()->route($destination)
            ->with('success', 'Periode Tracer Study berhasil dibuat sebagai draf.');
    }

    public function updatePeriod(Request $request, TracerStudyPeriode $periode)
    {
        if ($periode->status === TracerStudyPeriode::STATUS_CLOSED) {
            return back()->with('error', 'Periode yang telah ditutup tidak dapat diubah.');
        }

        $validated = $request->validate([
            'nama' => 'nullable|string|max:120',
            'keterangan' => 'nullable|string|max:5000',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $periode->update([
            'nama' => filled($validated['nama'] ?? null) ? trim($validated['nama']) : null,
            'keterangan' => $request->has('keterangan')
                ? (filled($validated['keterangan'] ?? null) ? trim($validated['keterangan']) : null)
                : $periode->keterangan,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
        ]);

        return back()->with('success', 'Informasi periode berhasil diperbarui.');
    }

    public function openPeriod(TracerStudyPeriode $periode)
    {
        try {
            DB::transaction(function () use ($periode): void {
                $lockedPeriod = TracerStudyPeriode::query()->lockForUpdate()->findOrFail($periode->id);

                if ($lockedPeriod->status === TracerStudyPeriode::STATUS_CLOSED) {
                    throw ValidationException::withMessages([
                        'periode' => 'Periode yang sudah ditutup tidak dapat dibuka kembali agar data historis tetap read-only.',
                    ]);
                }

                $otherOpenPeriodExists = TracerStudyPeriode::query()
                    ->where('status', TracerStudyPeriode::STATUS_OPEN)
                    ->where('id', '!=', $lockedPeriod->id)
                    ->exists();

                if ($otherOpenPeriodExists) {
                    throw ValidationException::withMessages([
                        'periode' => 'Tutup periode terbuka saat ini sebelum membuka periode lain.',
                    ]);
                }

                $lockedPeriod->status = TracerStudyPeriode::STATUS_OPEN;
                $lockedPeriod->active_key = 'tracer-study-open';
                $lockedPeriod->save();
            });
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                throw ValidationException::withMessages([
                    'periode' => 'Periode lain sedang dibuka oleh Admin BKK. Muat ulang halaman dan periksa status periode.',
                ]);
            }

            throw $exception;
        }

        return back()->with('success', 'Periode berhasil dibuka. Respons hanya diterima selama rentang tanggal periode.');
    }

    public function closePeriod(TracerStudyPeriode $periode)
    {
        DB::transaction(function () use ($periode): void {
            $lockedPeriod = TracerStudyPeriode::query()->lockForUpdate()->findOrFail($periode->id);
            $lockedPeriod->status = TracerStudyPeriode::STATUS_CLOSED;
            $lockedPeriod->active_key = null;
            $lockedPeriod->save();
        });

        return back()->with('success', 'Periode ditutup. Respons dan histori tetap tersimpan, tetapi alumni tidak dapat mengubahnya.');
    }

    public function show(TracerStudy $tracerStudy)
    {
        $tracerStudy->load([
            'user:id,name,email',
            'periode',
            'verifier:id,name',
        ]);

        return view('dashboard.admin.tracer-study.show', compact('tracerStudy'));
    }

    public function export(Request $request, TracerStudyExcelExport $exporter)
    {
        $filters = $this->validatedFilters($request, true);
        $periode = TracerStudyPeriode::query()->findOrFail($filters['periode_id']);
        $responses = $this->responseQuery($request, $periode->id)
            ->with('periode:id,tahun,nama')
            ->orderBy('nama_lengkap')
            ->get();

        try {
            $path = $exporter->create($responses);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return response()->download(
            $path,
            'tracer-study-' . $periode->tahun . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    private function validatedFilters(Request $request, bool $periodRequired): array
    {
        return $request->validate([
            'periode_id' => [
                $periodRequired ? 'required' : 'nullable',
                'integer',
                Rule::exists('tracer_studies', 'id')->where(fn ($query) => $query->where('record_type', 'period')),
            ],
            'tahun_lulus' => 'nullable|integer|min:1950|max:' . (now()->year + 1),
            'jurusan' => 'nullable|string|max:255',
            'status_kegiatan' => 'nullable|in:bekerja,magang',
            'status_verifikasi' => [
                'nullable',
                Rule::in([
                    TracerStudy::STATUS_UNVERIFIED,
                    TracerStudy::STATUS_VERIFIED,
                    TracerStudy::STATUS_NEEDS_UPDATE,
                    TracerStudy::STATUS_FOLLOW_UP,
                ]),
            ],
            'search' => 'nullable|string|max:100',
        ]);
    }

    private function responseQuery(Request $request, int $periodeId)
    {
        $filters = $this->validatedFilters($request, false);
        $query = TracerStudy::query()->where('periode_id', $periodeId);

        if (filled($filters['tahun_lulus'] ?? null)) {
            $query->where('tahun_lulus', $filters['tahun_lulus']);
        }

        if (filled($filters['jurusan'] ?? null)) {
            $query->where('jurusan', $filters['jurusan']);
        }

        if (filled($filters['status_kegiatan'] ?? null)) {
            $query->where('status_kegiatan', $filters['status_kegiatan']);
        }

        if (filled($filters['status_verifikasi'] ?? null)) {
            $query->where('status_verifikasi', $filters['status_verifikasi']);
        }

        if (filled($filters['search'] ?? null)) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search): void {
                $builder->where('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('nama_perusahaan', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return in_array((string) $exception->getCode(), ['23000', '23505'], true);
    }
}
