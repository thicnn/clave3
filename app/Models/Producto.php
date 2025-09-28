<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria',
        'maquina_id',
        'tipo_impresion',
        'insumo_id',
        'precio_venta',
        'costo_produccion',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'precio_venta' => 'float',
        'costo_produccion' => 'float',
        'activo' => 'boolean',
    ];

    /**
     * Relación con la máquina
     */
    public function maquina()
    {
        return $this->belongsTo(Maquina::class);
    }

    /**
     * Relación con el insumo
     */
    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    /**
     * Relación con pedido_items
     */
    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }

    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para productos por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Calcula el costo de producción basado en insumo y máquina
     */
    public function calcularCostoProduccion()
    {
        if ($this->categoria === 'servicio') {
            return 0; // Los servicios no tienen costo de producción
        }

        if (!$this->insumo || !$this->maquina) {
            return 0;
        }

        $costoInsumo = $this->insumo->costo_por_unidad;
        $costoImpresion = $this->tipo_impresion === 'color' 
            ? $this->maquina->costo_color_carilla 
            : $this->maquina->costo_bn_carilla;

        return $costoInsumo + $costoImpresion;
    }

    /**
     * Calcula la ganancia del producto
     */
    public function calcularGanancia()
    {
        return $this->precio_venta - $this->costo_produccion;
    }

    /**
     * Calcula el margen de ganancia en porcentaje
     */
    public function calcularMargenGanancia()
    {
        if ($this->costo_produccion == 0) {
            return 0;
        }
        return (($this->precio_venta - $this->costo_produccion) / $this->costo_produccion) * 100;
    }

    /**
     * Actualiza el costo de producción automáticamente
     */
    public function actualizarCostoProduccion()
    {
        $this->costo_produccion = $this->calcularCostoProduccion();
        $this->save();
    }
}
