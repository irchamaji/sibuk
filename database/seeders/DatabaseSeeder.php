<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Perizinan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed akun pengguna untuk simulasi audit keamanan.
     * Informasi akun ini ditampilkan di halaman login (card kuning audit).
     * Menggunakan firstOrCreate agar aman dijalankan ulang.
     */
    public function run(): void
    {
        // Akun Pengguna Biasa (role: pengguna)
        $pengguna = User::firstOrCreate(
            ['email' => 'pengguna@email.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('Passwrod123'),
                'role'     => 'pengguna',
            ]
        );

        // Akun Admin Pemerintah Daerah (role: admin-pemda)
        User::firstOrCreate(
            ['email' => 'admin@pemda.com'],
            [
                'name'     => 'Admin Pemda',
                'password' => Hash::make('Passwrod123'),
                'role'     => 'admin-pemda',
            ]
        );

        // Akun Superadmin (role: superadmin) - akses penuh
        User::firstOrCreate(
            ['email' => 'superadmin@pemda.com'],
            [
                'name'     => 'Super Administrator',
                'password' => Hash::make('Passwrod123'),
                'role'     => 'superadmin',
            ]
        );

        // Seed contoh perizinan untuk demo (hanya jika belum ada)
        if ($pengguna->perizinans()->count() === 0) {
            Perizinan::create([
                'user_id'      => $pengguna->id,
                'nama_pemohon' => 'Budi Santoso',
                'nama_usaha'   => 'Warung Makan Pak Budi',
                'lokasi_usaha' => 'Jl. Merdeka No. 10, Kelurahan Sukamaju, Kecamatan Tengah, Kota Contoh',
                'omzet'        => 5000000,
                'status'       => 'Pengajuan',
            ]);

            Perizinan::create([
                'user_id'      => $pengguna->id,
                'nama_pemohon' => 'Budi Santoso',
                'nama_usaha'   => 'Toko Kelontong Santoso',
                'lokasi_usaha' => 'Jl. Pahlawan No. 5, Kelurahan Damai, Kecamatan Selatan, Kota Contoh',
                'omzet'        => 3000000,
                'status'       => 'Diizinkan',
                'catatan_admin' => 'Dokumen lengkap, usaha sudah berjalan.',
            ]);
        }
    }
}
