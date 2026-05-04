<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manajemen Sesi: Cek timeout sesi login.
 * Sesi expired setelah 30 menit tidak aktif.
 */
class CheckSessionTimeout
{
    // Durasi maksimal sesi: 30 menit (dalam detik)
    const SESSION_TIMEOUT = 1800;

    public function handle(Request $request, Closure $next): Response
    {
        // Manajemen Sesi: skip cek jika user belum login
        if (!Auth::check()) {
            return $next($request);
        }

        $lastActivity = session('last_activity_time');

        if ($lastActivity !== null) {
            $idleTime = time() - $lastActivity;

            // Manajemen Sesi: sesi expired jika idle lebih dari 30 menit
            if ($idleTime > self::SESSION_TIMEOUT) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir karena tidak aktif selama 30 menit. Silakan login kembali.');
            }
        }

        // Manajemen Sesi: perbarui waktu aktivitas terakhir
        session(['last_activity_time' => time()]);

        return $next($request);
    }
}
