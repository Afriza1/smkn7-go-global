<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountStatusAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $routeName = $request->route()?->getName();

        if (! $user || $routeName === 'logout') {
            return $next($request);
        }

        if ($user->status === 'dinonaktifkan' || ($user->status === 'ditolak' && $user->role !== 'siswa')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => $user->status === 'dinonaktifkan'
                    ? 'Akun Anda telah dinonaktifkan oleh pihak sekolah. Silakan hubungi Administrator SMKN 7 Jember.'
                    : 'Pendaftaran akun Anda ditolak atau memerlukan perbaikan data.',
            ]);
        }

        if ($user->role === 'guru' && ! $user->isAktif()) {
            abort(403, 'Akun Guru Anda belum aktif dan belum dapat mengakses fitur Guru.');
        }

        if ($user->role === 'siswa' && $user->status !== 'aktif') {
            if (in_array($routeName, ['dashboard.siswa', 'dashboard.siswa.verification.resubmit'], true)) {
                return $next($request);
            }

            return redirect()->route('dashboard.siswa')
                ->with('error', 'Akses fitur siswa tersedia setelah akun disetujui Guru.');
        }

        return $next($request);
    }
}
