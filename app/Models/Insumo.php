<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;

class Insumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo',
        'stock_actual',
        'stock_minimo',
    ];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class)->withPivot('cantidad');
    }
}