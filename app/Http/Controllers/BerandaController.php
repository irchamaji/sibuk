<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BerandaController extends Controller
{
    /**
     * Tampilkan halaman beranda sesuai role pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        // Hitung statistik perizinan milik pengguna (untuk role pengguna)
        $statistik = [];
        if ($user->role === 'pengguna') {
            $statistik = [
                'total'     => $user->perizinans()->count(),
                'pengajuan' => $user->perizinans()->where('status', 'Pengajuan')->count(),
                'diizinkan' => $user->perizinans()->where('status', 'Diizinkan')->count(),
                'ditolak'   => $user->perizinans()->where('status', 'Ditolak')->count(),
            ];
        }

        // Hitung statistik global untuk admin
        if (in_array($user->role, ['admin-pemda', 'superadmin'])) {
            $statistik = [
                'total'     => \App\Models\Perizinan::count(),
                'pengajuan' => \App\Models\Perizinan::where('status', 'Pengajuan')->count(),
                'diizinkan' => \App\Models\Perizinan::where('status', 'Diizinkan')->count(),
                'ditolak'   => \App\Models\Perizinan::where('status', 'Ditolak')->count(),
            ];
        }

        return Inertia::render('Beranda', [
            'user'      => $user,
            'statistik' => $statistik,
        ]);
    }
}
