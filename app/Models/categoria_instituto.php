<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categoria_instituto extends Model
{
    protected $table = 'categoria_instituto';
    protected $primaryKey = 'id_categoria_inst';

    protected $fillable = [
        'nombre',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'categoria_instituto_id', 'id_categoria_inst');
    }
}
