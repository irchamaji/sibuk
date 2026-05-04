<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel perizinan UMKM.
     * ID sequential (bukan UUID) - intentional untuk simulasi IDOR audit.
     */
    public function up(): void
    {
        Schema::create('perizinans', function (Blueprint $table) {
            // Primary key sequential numeric - intentional IDOR vector
            $table->id();

            // Relasi ke user pemohon
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Data Pengajuan Izin
            $table->string('nama_pemohon');
            $table->string('nama_usaha');
            $table->text('lokasi_usaha');

            // File Upload: path file surat kepemilikan
            // Tidak ada validasi format file - intentional untuk simulasi audit
            $table->string('surat_kepemilikan')->nullable();

            // Omzet dalam satuan rupiah (angka saja, tanpa format)
            $table->bigInteger('omzet')->default(0);

            // Status Perizinan: alur dari Pengajuan ke Diizinkan/Ditolak
            $table->enum('status', ['Pengajuan', 'Diizinkan', 'Ditolak'])->default('Pengajuan');

            // Catatan dari admin saat mengubah status
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinans');
    }
};
