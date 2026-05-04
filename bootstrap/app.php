<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckSessionTimeout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Inertia: handle middleware untuk share data ke semua halaman
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Daftarkan alias middleware kustom
        $middleware->alias([
            // RBAC: cek role pengguna
            'role' => CheckRole::class,
            // Manajemen Sesi: cek timeout sesi
            'session.timeout' => CheckSessionTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
