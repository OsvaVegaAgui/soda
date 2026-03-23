<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaDiaria extends Model
{
    protected $table = 'caja_diaria';

    protected $fillable = [
        'user_id',
        'fecha',
        'monto',
        'observacion',
        'cerrada',
        'hora_apertura',
        'hora_cierre',
    ];

    protected $casts = [
        'cerrada'       => 'boolean',
        'hora_apertura' => 'datetime',
        'hora_cierre'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
