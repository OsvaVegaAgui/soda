<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';
    protected $primaryKey = 'idPais';

    public $timestamps = false; 

    protected $fillable = [
        'nombre', 
        'extension', 
        'fecha_independencia', 
        'habitantes'
    ];

   
}