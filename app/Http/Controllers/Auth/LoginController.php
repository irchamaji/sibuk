<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Proses login pengguna.
     * Validasi Input Form + Manajemen Login Gagal + Manajemen Sesi
     */
    public function store(Request $request)
    {
        // Validasi Input Form: pastikan email dan password diisi
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek apakah akun ada di database
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        // Manajemen Login Gagal: cek apakah akun sedang terkunci
        if ($user->isLocked()) {
            $detikSisa = $user->locked_until->diffInSeconds(now());
            $menitSisa = ceil($detikSisa / 60);
            return back()->withErrors([
                'email' => "Akun Anda dikunci. Coba lagi dalam {$menitSisa} menit.",
            ]);
        }

        // Coba autentikasi dengan kredensial yang diberikan
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Manajemen Login Gagal: tambah counter gagal
            $user->increment('failed_attempts');

            // Manajemen Login Gagal: kunci akun setelah 5 kali gagal selama 1 menit
            if ($user->failed_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinute()]);
                return back()->withErrors([
                    'email' => 'Akun Anda dikunci selama 1 menit karena terlalu banyak percobaan login gagal.',
                ]);
            }

            $sisaPercobaan = 5 - $user->failed_attempts;
            return back()->withErrors([
                'email' => "Email atau password salah. Sisa percobaan: {$sisaPercobaan} kali.",
            ]);
        }

        // Manajemen Login Gagal: reset counter setelah login berhasil
        $user->update(['failed_attempts' => 0, 'locked_until' => null]);

        // Manajemen Sesi: buat sesi baru dan catat waktu aktivitas
        $request->session()->regenerate();
        session(['last_activity_time' => time()]);

        return redirect()->intended(route('beranda'));
    }

    /**
     * Logout dan hapus sesi aktif.
     * Manajemen Sesi: invalidasi sesi setelah logout
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        // Manajemen Sesi: hapus semua data sesi dan regenerasi token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
