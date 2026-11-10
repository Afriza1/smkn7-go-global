<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
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

        // Check account status
        if ($user && $user->status === 'dinonaktifkan') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan oleh pihak sekolah. Silakan hubungi Administrator SMKN 7 Jember.',
            ]);
        }

        if ($user && $user->status === 'ditolak' && $user->role !== 'siswa') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Pendaftaran akun Anda ditolak atau memerlukan perbaikan data. Silakan hubungi bagian tata usaha SMKN 7 Jember.',
            ]);
        }

        if ($user && $user->role === 'guru' && ! $user->isAktif()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match ($user->status) {
                'menunggu_verifikasi' => 'Akun Guru Anda masih menunggu verifikasi sekolah. Silakan hubungi Administrator SMKN 7 Jember setelah proses verifikasi selesai.',
                default => 'Akun Guru Anda belum aktif. Silakan hubungi Administrator SMKN 7 Jember.',
            };

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        // Role-based redirection as per guidelines
        return match ($user->role) {
            'kepala_sekolah' => redirect()->route('dashboard.kepala-sekolah'),
            'admin_sekolah' => redirect()->route('dashboard.admin'),
            'siswa' => redirect()->route('dashboard.siswa'),
            'alumni' => redirect()->route('dashboard.alumni'),
            'orang_tua' => redirect()->route('dashboard.orang-tua'),
            'guru' => redirect()->route('dashboard.guru'),
            default => redirect()->route('dashboard.role-placeholder', ['role' => $user->role]),
        };
    }
}
