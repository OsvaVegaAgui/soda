<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar en masa.
     *
     * @var list<string>
     */
        protected $fillable = [
        'name',
        'rol',
        'email',
        'password',
        'activo',
        'reset_token',
        'reset_token_date', 
    ];
    /**
     * Los atributos que deben ocultarse al serializar el modelo.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'activo' => 'boolean',
            'reset_token_date' => 'datetime', // ← FALTABA
        ];
    }


    /**
     * Scope para obtener solo los usuarios activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }
}
