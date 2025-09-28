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
        'precio',
        'costo_por_unidad',
        'stock_actual',
        'stock_minimo',
    ];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class)->withPivot('cantidad');
    }

    /**
     * Relación con pedido_items
     */
    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }

    /**
     * Calcular precio de venta basado en el costo
     */
    public function calcularPrecioVenta(): float
    {
        if ($this->precio) {
            return $this->precio;
        }

        return \App\Models\Configuracion::calcularPrecioVenta($this->costo);
    }

    /**
     * Verificar si el stock está bajo el mínimo
     */
    public function stockBajo(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    /**
     * Obtener el porcentaje de stock restante
     */
    public function porcentajeStock(): float
    {
        if ($this->stock_minimo == 0) {
            return 100;
        }
        
        return min(100, ($this->stock_actual / $this->stock_minimo) * 100);
    }
}