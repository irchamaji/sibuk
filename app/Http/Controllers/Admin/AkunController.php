<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AkunController extends Controller
{
    /**
     * Daftar semua akun pengguna (superadmin only).
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Akun/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Buat akun pengguna baru (superadmin only).
     * Validasi Input Form + Validasi Password
     */
    public function store(Request $request)
    {
        // Validasi Input Form: cek semua field akun baru
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role'  => 'required|in:pengguna,admin-pemda,superadmin',
            // Validasi Password: min 8, huruf besar+kecil, angka (tanpa simbol - intentional)
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah terdaftar.',
            'role.required'  => 'Role wajib dipilih.',
            'role.in'        => 'Role tidak valid.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,   // cast 'hashed' di model otomatis hash
            'role'     => $request->role,
        ]);

        return back()->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Perbarui data akun (superadmin only).
     * Validasi Input Form
     */
    public function update(Request $request, User $user)
    {
        // Validasi Input Form: nama, email, dan role wajib valid
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:pengguna,admin-pemda,superadmin',
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
            'role.required'  => 'Role wajib dipilih.',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Reset password akun oleh superadmin.
     * Tidak perlu verifikasi password lama.
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update([
            'password' => $request->password,   // cast 'hashed' di model otomatis hash
        ]);

        return back()->with('success', 'Password akun ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus akun pengguna (superadmin only).
     * Tidak bisa hapus akun sendiri.
     */
    public function destroy(User $user)
    {
        // Cegah superadmin menghapus akun sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun sendiri.']);
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
