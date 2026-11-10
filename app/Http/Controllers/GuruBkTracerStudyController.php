<?php

namespace App\Http\Controllers;

use App\Models\TracerStudy;
use App\Models\TracerStudyPeriode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GuruBkTracerStudyController extends Controller
{
    public function statistics(Request $request)
    {
        return app(KepalaSekolahTracerStudyController::class)->index($request);
    }

    public function index()
    {
        $periods = TracerStudyPeriode::query()
            ->withCount('tracerStudies')
            ->orderByDesc('tahun')
            ->get();

        $activePeriod = $periods->firstWhere('status', TracerStudyPeriode::STATUS_OPEN);
        $totalResponses = $periods->sum('tracer_studies_count');
        $alumniCount = User::query()->where('role', 'alumni')->count();
        $responseRate = $activePeriod && $alumniCount > 0
            ? round(($activePeriod->tracer_studies_count / $alumniCount) * 100, 1)
            : null;

        $stats = [
            'total_periode' => $periods->count(),
            'periode_aktif' => $periods->where('status', TracerStudyPeriode::STATUS_OPEN)->count(),
            'total_respons' => $totalResponses,
            'periode_ditutup' => $periods->where('status', TracerStudyPeriode::STATUS_CLOSED)->count(),
        ];

        return view('dashboard.guru.tracer-study.index', compact(
            'periods', 'activePeriod', 'stats', 'alumniCount', 'responseRate'
        ));
    }

    public function show(Request $request, TracerStudyPeriode $periode)
    {
        $periode->loadCount('tracerStudies');
        $alumniCount = User::query()->where('role', 'alumni')->count();
        $responseRate = $alumniCount > 0
            ? round(($periode->tracer_studies_count / $alumniCount) * 100, 1)
            : null;
        $filters = $request->validate([
            'search' => 'nullable|string|max:100',
            'status_verifikasi' => ['nullable', Rule::in([
                TracerStudy::STATUS_UNVERIFIED,
                TracerStudy::STATUS_VERIFIED,
                TracerStudy::STATUS_NEEDS_UPDATE,
                TracerStudy::STATUS_FOLLOW_UP,
            ])],
        ]);

        $responsesQuery = $periode->tracerStudies();
        $stats = [
            'total' => (clone $responsesQuery)->count(),
            'belum_diverifikasi' => (clone $responsesQuery)->whereIn('status_verifikasi', [
                TracerStudy::STATUS_UNVERIFIED,
                TracerStudy::STATUS_FOLLOW_UP,
            ])->count(),
            'terverifikasi' => (clone $responsesQuery)->where('status_verifikasi', TracerStudy::STATUS_VERIFIED)->count(),
            'perlu_perbaikan' => (clone $responsesQuery)->where('status_verifikasi', TracerStudy::STATUS_NEEDS_UPDATE)->count(),
        ];

        $responses = $responsesQuery
            ->with('user:id,name,email')
            ->when(filled($filters['status_verifikasi'] ?? null), fn ($query) => $query->where('status_verifikasi', $filters['status_verifikasi']))
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters): void {
                $search = trim($filters['search']);
                $query->where(function ($builder) use ($search): void {
                    $builder->where('nama_lengkap', 'like', '%' . $search . '%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.guru.tracer-study.show', compact(
            'periode', 'alumniCount', 'responseRate', 'responses', 'stats', 'filters'
        ));
    }

    public function showResponse(TracerStudyPeriode $periode, TracerStudy $tracerStudy)
    {
        $response = $periode->tracerStudies()
            ->with(['user:id,name,email', 'verifier:id,name'])
            ->findOrFail($tracerStudy->id);

        return view('dashboard.guru.tracer-study.response', compact('periode', 'response'));
    }

    public function verify(Request $request, TracerStudyPeriode $periode, TracerStudy $tracerStudy)
    {
        $validated = $request->validate(['catatan' => 'nullable|string|max:3000']);

        $actorId = (int) $request->user()->getAuthIdentifier();
        DB::transaction(function () use ($periode, $tracerStudy, $validated, $actorId): void {
            $response = $periode->tracerStudies()->lockForUpdate()->findOrFail($tracerStudy->id);
            $previousStatus = $response->status_verifikasi;

            if ($previousStatus === TracerStudy::STATUS_VERIFIED) {
                return;
            }

            $response->status_verifikasi = TracerStudy::STATUS_VERIFIED;
            $response->diverifikasi_oleh = $actorId;
            $response->diverifikasi_pada = now();
            $response->save();

            $this->recordReviewHistory(
                $response,
                $previousStatus,
                TracerStudy::STATUS_VERIFIED,
                filled($validated['catatan'] ?? null) ? trim($validated['catatan']) : null,
                $actorId,
            );
        });

        return back()->with('success', 'Respons berhasil ditandai terverifikasi.');
    }

    public function requestRevision(Request $request, TracerStudyPeriode $periode, TracerStudy $tracerStudy)
    {
        $validated = $request->validate(['catatan' => 'required|string|max:3000']);

        if (! $periode->isPengisianTerbuka()) {
            return back()->with('error', 'Perbaikan hanya dapat diminta selama periode pengisian masih terbuka.');
        }

        $actorId = (int) $request->user()->getAuthIdentifier();
        DB::transaction(function () use ($periode, $tracerStudy, $validated, $actorId): void {
            $lockedPeriod = TracerStudyPeriode::query()->lockForUpdate()->findOrFail($periode->id);
            if (! $lockedPeriod->isPengisianTerbuka()) {
                abort(422, 'Periode pengisian sudah ditutup.');
            }

            $response = $lockedPeriod->tracerStudies()->lockForUpdate()->findOrFail($tracerStudy->id);
            $previousStatus = $response->status_verifikasi;
            $note = trim($validated['catatan']);

            $response->status_verifikasi = TracerStudy::STATUS_NEEDS_UPDATE;
            $response->diverifikasi_oleh = $actorId;
            $response->diverifikasi_pada = now();
            $response->save();

            $response->appendFollowUp([
                'admin_id' => $actorId,
                'jenis' => 'request_update',
                'pesan_alumni' => $note,
                'catatan_internal' => null,
                'diberitahukan_pada' => now(),
            ]);

            $this->recordReviewHistory($response, $previousStatus, TracerStudy::STATUS_NEEDS_UPDATE, $note, $actorId);
        });

        return back()->with('success', 'Catatan perbaikan berhasil dikirim kepada alumni.');
    }

    private function recordReviewHistory(TracerStudy $response, string $previousStatus, string $newStatus, ?string $note, int $actorId): void
    {
        $statusLabel = fn (string $status): string => match ($status) {
            TracerStudy::STATUS_VERIFIED => 'Terverifikasi',
            TracerStudy::STATUS_NEEDS_UPDATE => 'Perlu Perbaikan',
            TracerStudy::STATUS_FOLLOW_UP => 'Dalam Tindak Lanjut',
            default => 'Belum Diverifikasi',
        };
        $summary = sprintf('Pemeriksaan Guru BK: status %s menjadi %s.', $statusLabel($previousStatus), $statusLabel($newStatus));
        if ($note !== null) {
            $summary .= ' Catatan: ' . $note;
        }

        $response->appendHistory([
            'user_id' => $actorId,
            'ringkasan_perubahan' => $summary,
        ]);
    }
}
