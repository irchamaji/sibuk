<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AkunController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akun.
     */
    public function edit()
    {
        return Inertia::render('Akun/Pengaturan', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Perbarui data profil pengguna.
     * Validasi Input Form: cek nama dan email
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi Input Form: nama dan email wajib valid
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ganti password pengguna.
     * Validasi Password: sama dengan aturan registrasi
     */
    public function updatePassword(Request $request)
    {
        // Validasi Input Form + Validasi Password
        $request->validate([
            'current_password' => 'required',
            // Validasi Password: min 8, huruf besar+kecil, angka - tanpa simbol (intentional)
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        // Verifikasi password lama sebelum ganti
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
