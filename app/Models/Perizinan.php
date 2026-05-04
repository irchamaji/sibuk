<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perizinan extends Model
{
    protected $fillable = [
        'user_id',
        'nama_pemohon',
        'nama_usaha',
        'lokasi_usaha',
        'surat_kepemilikan',
        'omzet',
        'status',
        'catatan_admin',
    ];

    // Relasi: perizinan dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Warna badge status untuk tampilan UI
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Diizinkan' => 'success',
            'Ditolak'   => 'danger',
            default     => 'info',
        };
    }
}
