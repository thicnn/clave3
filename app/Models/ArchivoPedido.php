<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido; // <-- Añade esta línea

class ArchivoPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'nombre_original',
        'ruta',
        'impreso',
    ];

    /**
     * Define la relación: un archivo pertenece a un pedido.
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}