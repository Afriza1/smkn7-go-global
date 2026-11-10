<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check if user's role matches any of the required roles
        if (! in_array($user->role, $roles, true)) {
            // Redirect to their own dashboard with warning
            $ownDashboard = match ($user->role) {
                'kepala_sekolah' => route('dashboard.kepala-sekolah'),
                'admin_sekolah' => route('dashboard.admin'),
                'siswa' => route('dashboard.siswa'),
                'alumni' => route('dashboard.alumni'),
                'orang_tua' => route('dashboard.orang-tua'),
                'guru' => route('dashboard.guru'),
                default => route('dashboard.role-placeholder', ['role' => $user->role]),
            };

            return redirect($ownDashboard)->with('error', 'Akses ditolak: Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }

        // A Guru account must be approved before entering any Guru route.
        // A 403 avoids redirecting back to the same role-protected dashboard.
        if ($user->role === 'guru' && ! $user->isAktif()) {
            abort(403, 'Akun Guru Anda belum aktif dan belum dapat mengakses fitur Guru.');
        }

        return $next($request);
    }
}
