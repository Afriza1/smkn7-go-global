<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccountVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SiswaVerificationController extends Controller
{
    public function resubmit(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user->role === 'siswa' && $user->status === 'ditolak', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:25'],
        ]);

        DB::transaction(function () use ($user, $data): void {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedUser->role === 'siswa' && $lockedUser->status === 'ditolak', 409);

            $latestRequest = UserAccountVerificationRequest::query()
                ->where('user_id', $lockedUser->id)
                ->orderByDesc('submission_number')
                ->lockForUpdate()
                ->first();

            abort_if($latestRequest && $latestRequest->status !== UserAccountVerificationRequest::STATUS_REJECTED, 409);

            $submissionNumber = ($latestRequest?->submission_number ?? 0) + 1;
            $lockedUser->forceFill([...$data, 'status' => 'menunggu_verifikasi'])->save();

            UserAccountVerificationRequest::create([
                'user_id' => $lockedUser->id,
                'submission_number' => $submissionNumber,
                'status' => UserAccountVerificationRequest::STATUS_PENDING,
            ]);
        });

        return redirect()->route('dashboard.siswa')->with('success', 'Perbaikan data berhasil diajukan. Akun kembali menunggu verifikasi Guru.');
    }
}
