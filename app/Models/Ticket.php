<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'ticket';
    protected $primaryKey = 'id_ticket';

    protected $fillable = [
        'nombre', 'codigo', 'categoria_id', 'categoria_instituto_id', 'precio', 'cantidad',
    ];

    protected $casts = [
        'precio'   => 'decimal:2',
        'cantidad' => 'integer',
    ];

    // Relación: ticket pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(CategoriaTicket::class, 'categoria_id', 'id_categoria');
    }

    public function categoriaInstituto()
    {
        return $this->belongsTo(categoria_instituto::class, 'categoria_instituto_id', 'id_categoria_inst');
    }

    public function categoriaTicket()
    {
        return $this->belongsTo(categoriaTicket::class, 'categoria_id', 'id_categoria');
    }

    public function categoria_instituto()
    {
        return $this->belongsTo(categoria_instituto::class, 'categoria_instituto_id', 'id_categoria_inst');
    }
}
