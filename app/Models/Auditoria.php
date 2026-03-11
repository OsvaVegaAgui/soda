<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'id_auditoria';

    protected $fillable = [
        'user_id',
        'tabla',
        'registro_id',
        'accion',
        'valores_antes',
        'valores_despues',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
