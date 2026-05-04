<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Field yang boleh diisi massal
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'failed_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            // Manajemen Login Gagal: cast locked_until sebagai datetime
            'locked_until'      => 'datetime',
        ];
    }

    // Relasi: satu user bisa punya banyak perizinan
    public function perizinans()
    {
        return $this->hasMany(Perizinan::class);
    }

    // Cek apakah akun sedang terkunci (Manajemen Login Gagal)
    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    // Cek role user (RBAC)
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }
}
