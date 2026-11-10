<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = Auth::user();

        // Informative flash message regarding verification status
        session()->flash('status', 'Pendaftaran akun ' . ($user ? $user->getRoleDisplayName() : '') . ' berhasil disimpan! Akun Anda sedang dalam proses verifikasi pihak sekolah SMKN 7 Jember.');

        return match ($user->role ?? null) {
            'siswa' => redirect()->route('dashboard.siswa'),
            'alumni' => redirect()->route('dashboard.alumni'),
            'orang_tua' => redirect()->route('dashboard.orang-tua'),
            'guru' => redirect()->route('dashboard.guru'),
            default => redirect()->route('dashboard.role-placeholder', ['role' => $user->role ?? 'user']),
        };
    }
}
