<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PerizinanController extends Controller
{
    /**
     * Daftar perizinan milik pengguna yang sedang login.
     */
    public function index()
    {
        $perizinans = Auth::user()->perizinans()
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Perizinan/Index', [
            'perizinans' => $perizinans,
        ]);
    }

    /**
     * Tampilkan form pengajuan izin baru.
     */
    public function create()
    {
        return Inertia::render('Perizinan/Create');
    }

    /**
     * Simpan pengajuan izin baru.
     * Validasi Input Form + File Upload Validasi (minimal - intentional)
     */
    public function store(Request $request)
    {
        // Validasi Input Form: cek semua field wajib
        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nama_usaha'   => 'required|string|max:255',
            'lokasi_usaha' => 'required|string',
            'omzet'        => 'required|integer|min:0',
            // File Upload Validasi: hanya cek ukuran maksimal 5MB
            // Tidak ada validasi format/tipe file - intentional untuk simulasi audit
            'surat_kepemilikan' => 'nullable|file|max:5120',
        ], [
            'nama_pemohon.required'  => 'Nama pemohon wajib diisi.',
            'nama_usaha.required'    => 'Nama usaha wajib diisi.',
            'lokasi_usaha.required'  => 'Lokasi usaha wajib diisi.',
            'omzet.required'         => 'Omzet wajib diisi.',
            'omzet.integer'          => 'Omzet harus berupa angka.',
            'surat_kepemilikan.max'  => 'Ukuran file maksimal 5MB.',
        ]);

        // File Upload: simpan file ke storage jika ada
        $filePath = null;
        if ($request->hasFile('surat_kepemilikan')) {
            // File Upload Validasi: tidak ada cek tipe file (intentional)
            $filePath = $request->file('surat_kepemilikan')
                ->store('surat-kepemilikan', 'public');
        }

        // Simpan data perizinan baru dengan status default Pengajuan
        Perizinan::create([
            'user_id'           => Auth::id(),
            'nama_pemohon'      => $request->nama_pemohon,
            'nama_usaha'        => $request->nama_usaha,
            'lokasi_usaha'      => $request->lokasi_usaha,
            'surat_kepemilikan' => $filePath,
            'omzet'             => $request->omzet,
            'status'            => 'Pengajuan',
        ]);

        return redirect()->route('perizinan.index')
            ->with('success', 'Pengajuan izin berhasil dikirim. Status akan diperbarui oleh admin.');
    }

    /**
     * Tampilkan detail perizinan.
     * INTENTIONAL: Raw SQL query tanpa exception handling → SQL error leak ke user
     */
    public function show(string $id)
    {
        // INTENTIONAL VULNERABILITY: Raw SQL query dengan string concatenation
        // Tujuan: mensimulasikan kebocoran pesan error SQL mentah ke pengguna
        // Ini adalah skenario audit keamanan - jangan gunakan di produksi nyata
        $results = DB::select(
            "SELECT perizinans.*, users.name as nama_user, users.email as email_user
             FROM perizinans
             LEFT JOIN users ON users.id = perizinans.user_id
             WHERE perizinans.id = " . $id . "
             AND perizinans.user_id = " . Auth::id()
        );

        if (empty($results)) {
            abort(404, 'Perizinan tidak ditemukan.');
        }

        $perizinan = (object) (array) $results[0];

        return Inertia::render('Perizinan/Show', [
            'perizinan' => $perizinan,
        ]);
    }
}
