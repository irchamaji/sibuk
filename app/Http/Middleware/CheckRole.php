<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RBAC: Middleware cek role pengguna.
 * Gunakan di route: middleware('role:admin-pemda,superadmin')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Cek apakah role user ada dalam daftar role yang diizinkan
        if (!$request->user()->hasRole($roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
