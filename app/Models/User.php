<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'rol',
        'email',
        'password',
        'activo',
        'reset_token',
        'reset_token_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'activo'            => 'boolean',
            'reset_token_date'  => 'datetime',
        ];
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'user_id', 'id');
    }

    public function historialTiquetes()
    {
        return $this->hasMany(HistorialTiquetes::class, 'user_id', 'id');
    }

    public function auditoria()
    {
        return $this->hasMany(Auditoria::class, 'user_id', 'id');
    }
}
