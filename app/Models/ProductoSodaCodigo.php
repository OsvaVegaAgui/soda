<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoSodaCodigo extends Model
{
    protected $table      = 'producto_soda_codigos';
    public    $timestamps = false;

    protected $fillable = [
        'producto_soda_id',
        'codigo_barras',
    ];

    public function producto()
    {
        return $this->belongsTo(ProductoSoda::class, 'producto_soda_id', 'id_producto_soda');
    }
}
