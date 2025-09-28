<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido; // <-- LÍNEA AÑADIDA

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'rut',
        'direccion',
    ];

    /**
     * Define la relación: un cliente tiene muchos pedidos.
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}