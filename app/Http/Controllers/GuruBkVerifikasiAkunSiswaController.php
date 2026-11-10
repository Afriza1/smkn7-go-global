<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccountVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class GuruBkVerifikasiAkunSiswaController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in([
                UserAccountVerificationRequest::STATUS_PENDING,
                UserAccountVerificationRequest::STATUS_APPROVED,
                UserAccountVerificationRequest::STATUS_REJECTED,
            ])],
        ]);

        $status = $filters['status'] ?? UserAccountVerificationRequest::STATUS_PENDING;
        $requests = UserAccountVerificationRequest::query()
            ->with('user')
            ->where('status', $status)
            ->whereHas('user', fn ($query) => $query->where('role', 'siswa'))
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters): void {
                $search = trim($filters['search']);
                $query->whereHas('user', fn ($userQuery) => $userQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return response()->view('dashboard.guru.verifikasi-akun-siswa.index', compact('requests', 'filters', 'status'));
    }

    public function show(UserAccountVerificationRequest $verificationRequest): Response
    {
        abort_unless($verificationRequest->user()->where('role', 'siswa')->exists(), 404);
        $verificationRequest->load(['user', 'reviewer']);
        $history = UserAccountVerificationRequest::query()
            ->with('reviewer')
            ->where('user_id', $verificationRequest->user_id)
            ->orderByDesc('submission_number')
            ->get();

        return response()->view('dashboard.guru.verifikasi-akun-siswa.show', compact('verificationRequest', 'history'));
    }

    public function decide(Request $request, UserAccountVerificationRequest $verificationRequest): Response
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in([
                UserAccountVerificationRequest::STATUS_APPROVED,
                UserAccountVerificationRequest::STATUS_REJECTED,
            ])],
            'rejection_reason' => ['required_if:decision,ditolak', 'nullable', 'string', 'max:3000'],
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $verificationRequest, $data): void {
            $lockedRequest = UserAccountVerificationRequest::query()
                ->whereKey($verificationRequest->id)
                ->lockForUpdate()
                ->firstOrFail();
            $student = User::query()->whereKey($lockedRequest->user_id)->lockForUpdate()->firstOrFail();

            abort_unless($student->role === 'siswa', 404);
            abort_unless(
                $lockedRequest->status === UserAccountVerificationRequest::STATUS_PENDING
                    && $student->status === 'menunggu_verifikasi'
                    && (int) $student->accountVerificationRequests()->max('submission_number') === (int) $lockedRequest->submission_number,
                409,
                'Pengajuan ini telah diproses atau bukan pengajuan terbaru.'
            );

            $approved = $data['decision'] === UserAccountVerificationRequest::STATUS_APPROVED;
            $student->forceFill(['status' => $approved ? 'aktif' : 'ditolak'])->save();
            $lockedRequest->update([
                'status' => $data['decision'],
                'reviewer_id' => $request->user()->id,
                'reviewer_name_snapshot' => $request->user()->name,
                'reviewed_at' => now(),
                'rejection_reason' => $approved ? null : $data['rejection_reason'],
            ]);
        }, attempts: 3);

        return redirect()->route('dashboard.guru.verifikasi-akun-siswa.show', $verificationRequest)
            ->with('success', $data['decision'] === UserAccountVerificationRequest::STATUS_APPROVED
                ? 'Akun siswa berhasil disetujui.'
                : 'Akun siswa ditolak dan alasan keputusan tercatat.');
    }
}
