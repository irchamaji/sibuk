<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PerizinanController extends Controller
{
    /**
     * Daftar semua perizinan (untuk admin).
     * Admin bisa melihat perizinan dari semua pengguna.
     */
    public function index(Request $request)
    {
        // Filter berdasarkan status jika ada query parameter
        $query = Perizinan::with('user')->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $perizinans = $query->get();

        return Inertia::render('Admin/Perizinan/Index', [
            'perizinans'    => $perizinans,
            'filterStatus'  => $request->status ?? '',
        ]);
    }

    /**
     * Tampilkan detail perizinan untuk admin (dengan data user).
     */
    public function show(Perizinan $perizinan)
    {
        $perizinan->load('user');

        return Inertia::render('Admin/Perizinan/Show', [
            'perizinan' => $perizinan,
        ]);
    }

    /**
     * Perbarui status perizinan oleh admin.
     * Admin-pemda mengubah status dari Pengajuan ke Diizinkan/Ditolak.
     */
    public function update(Request $request, Perizinan $perizinan)
    {
        // Validasi Input Form: status harus valid
        $request->validate([
            'status'        => 'required|in:Pengajuan,Diizinkan,Ditolak',
            'catatan_admin' => 'nullable|string|max:1000',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
        ]);

        // Perbarui status dan catatan admin
        $perizinan->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', "Status perizinan berhasil diubah menjadi {$request->status}.");
    }
}
