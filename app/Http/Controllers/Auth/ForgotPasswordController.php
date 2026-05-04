<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form permintaan reset password.
     */
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Kirim link reset password ke email.
     * Validasi Input Form: cek format email
     */
    public function store(Request $request)
    {
        // Validasi Input Form: email harus valid dan terdaftar
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Kirim link reset password via email
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        }

        return back()->withErrors(['email' => 'Email tidak terdaftar atau gagal mengirim link reset.']);
    }

    /**
     * Tampilkan form reset password dengan token.
     */
    public function resetCreate(Request $request, string $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses reset password baru.
     * Validasi Password: sama seperti aturan registrasi
     */
    public function resetStore(Request $request)
    {
        // Validasi Input Form + Validasi Password
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ], [
            'email.required'     => 'Email wajib diisi.',
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        // Reset password dengan token yang valid
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
        }

        return back()->withErrors(['email' => 'Token tidak valid atau sudah kadaluarsa.']);
    }
}
