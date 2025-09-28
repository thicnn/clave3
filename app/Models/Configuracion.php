<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    /**
     * Obtener el valor de una configuración por su clave
     */
    public static function obtener(string $clave, $default = null)
    {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Establecer el valor de una configuración
     */
    public static function establecer(string $clave, string $valor, string $descripcion = null)
    {
        return static::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }

    /**
     * Calcular precio de venta basado en costo y configuraciones
     */
    public static function calcularPrecioVenta(float $costo): float
    {
        $margenGanancia = (float) static::obtener('margen_ganancia_default', 1.5);
        $factorOperativo = (float) static::obtener('factor_costo_operativo', 0.2);
        $precioMinimo = (float) static::obtener('precio_minimo_por_hoja', 0.1);

        $precioCalculado = $costo * $margenGanancia * (1 + $factorOperativo);
        
        return max($precioCalculado, $precioMinimo);
    }
}