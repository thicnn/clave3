<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'costo_bn_carilla',
        'costo_color_carilla',
        'activa',
        'descripcion',
    ];

    protected $casts = [
        'costo_bn_carilla' => 'decimal:2',
        'costo_color_carilla' => 'decimal:2',
        'activa' => 'boolean',
    ];

    /**
     * Relación con pedido_items
     */
    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }

    /**
     * Obtener el costo de impresión según el tipo
     */
    public function getCostoImpresion(string $tipo): ?float
    {
        if ($tipo === 'b&n') {
            return $this->costo_bn_carilla;
        } elseif ($tipo === 'color') {
            return $this->costo_color_carilla;
        }
        
        return null;
    }

    /**
     * Verificar si la máquina soporta impresión a color
     */
    public function soportaColor(): bool
    {
        return !is_null($this->costo_color_carilla);
    }

    /**
     * Scope para máquinas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}