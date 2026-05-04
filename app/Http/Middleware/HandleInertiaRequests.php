<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Template root halaman Inertia.
     */
    protected $rootView = 'app';

    /**
     * Data yang di-share ke semua halaman Vue (Inertia shared props).
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Data user yang sedang login
            'auth' => [
                'user' => $request->user() ? [
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'email' => $request->user()->email,
                    'role'  => $request->user()->role,
                ] : null,
            ],
            // Flash messages untuk notifikasi
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
        ]);
    }
}
