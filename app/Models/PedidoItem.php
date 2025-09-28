<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'es_doble_faz',
        'descuento_item',
        'precio_venta_item',
        'costo_produccion_item',
    ];

    protected $casts = [
        'es_doble_faz' => 'boolean',
        'descuento_item' => 'decimal:2',
        'precio_venta_item' => 'decimal:2',
        'costo_produccion_item' => 'decimal:2',
    ];

    /**
     * Relación con pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Relación con producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Calcular cantidad de hojas necesarias
     */
    public function calcularCantidadHojas(): int
    {
        if ($this->es_doble_faz) {
            return (int) ceil($this->cantidad / 2);
        }
        
        return $this->cantidad;
    }

    /**
     * Calcular costo de producción del ítem basado en el producto
     */
    public function calcularCostoProduccion(): float
    {
        if (!$this->producto) {
            return 0;
        }

        $costoBase = $this->producto->costo_produccion;
        $cantidadHojas = $this->calcularCantidadHojas();
        
        // Para productos de impresión/fotocopia, multiplicar por cantidad de hojas
        if (in_array($this->producto->categoria, ['impresion', 'fotocopia'])) {
            return $costoBase * $cantidadHojas;
        }
        
        // Para servicios, el costo es fijo por cantidad
        return $costoBase * $this->cantidad;
    }

    /**
     * Calcular precio de venta del ítem basado en el producto
     */
    public function calcularPrecioVenta(): float
    {
        if (!$this->producto) {
            return 0;
        }

        $precioBase = $this->producto->precio_venta;
        
        // Para productos de impresión/fotocopia, multiplicar por cantidad de hojas
        if (in_array($this->producto->categoria, ['impresion', 'fotocopia'])) {
            $cantidadHojas = $this->calcularCantidadHojas();
            $precioBase = $precioBase * $cantidadHojas;
        } else {
            // Para servicios, multiplicar por cantidad
            $precioBase = $precioBase * $this->cantidad;
        }
        
        // Aplicar descuento
        $montoDescuento = $precioBase * ($this->descuento_item / 100);
        
        return $precioBase - $montoDescuento;
    }

    /**
     * Scope para ítems de impresión
     */
    public function scopeImpresion($query)
    {
        return $query->whereHas('producto', function($q) {
            $q->whereIn('categoria', ['impresion', 'fotocopia']);
        });
    }

    /**
     * Scope para ítems de servicio
     */
    public function scopeServicios($query)
    {
        return $query->whereHas('producto', function($q) {
            $q->where('categoria', 'servicio');
        });
    }
}