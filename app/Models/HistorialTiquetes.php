<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialTiquetes extends Model
{
    protected $table = 'historial_tiquetes';
    protected $primaryKey = 'id_historial';

    public $timestamps = false;

    protected $fillable = [
        'id_ticket',
        'user_id',
        'cantidad_impresa',
    ];

    protected $casts = [
        'fecha_impresion' => 'datetime',
        'cantidad_impresa' => 'integer',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id_ticket', 'id_ticket');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
