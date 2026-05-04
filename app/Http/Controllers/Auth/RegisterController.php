<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Proses pendaftaran akun baru.
     * Validasi Input Form + Validasi Password
     */
    public function store(Request $request)
    {
        // Validasi Input Form: cek semua field registrasi
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            // Validasi Password: min 8 karakter, harus ada huruf besar, huruf kecil, dan angka
            // Tidak ada validasi karakter simbol (intentional - simulasi audit)
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()   // harus ada huruf besar dan kecil
                    ->numbers(),    // harus ada angka - tanpa symbols() (intentional)
            ],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        // Buat akun baru dengan role pengguna (default)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pengguna',
        ]);

        // Manajemen Sesi: login otomatis setelah registrasi
        Auth::login($user);
        $request->session()->regenerate();
        session(['last_activity_time' => time()]);

        return redirect()->route('beranda')->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }
}
