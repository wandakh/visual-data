<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Sebelumnya: App\Models\UserNew (nama aneh, sisa dari waktu ada 2 model User).
 * Sekarang dikonsolidasi jadi 1 model User standar, tabel tetap `users`.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Diperbaiki: sebelumnya asset('images/' . $image) bisa jadi broken image
     * kalau user belum pernah upload foto (image masih null). Sekarang ada
     * fallback avatar inisial (generated on-the-fly, gak perlu file tambahan).
     */
    public function profilePhotoUrl(): string
    {
        if ($this->image) {
            return asset('images/' . $this->image);
        }

        $initial = strtoupper(substr($this->name ?: '?', 0, 1));
        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect width="100" height="100" rx="50" fill="#6366f1"/>
            <text x="50" y="50" font-size="42" fill="#ffffff" text-anchor="middle" dominant-baseline="central" font-family="sans-serif">{$initial}</text>
        </svg>
        SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
