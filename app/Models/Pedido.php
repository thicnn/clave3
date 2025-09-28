<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Insumo;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'fecha_entrega',
        'estado',
        'total',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)->withPivot('cantidad');
    }
}