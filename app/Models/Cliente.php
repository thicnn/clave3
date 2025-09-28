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
        'notas',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot del modelo para asignar nombre automático si no se proporciona
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($cliente) {
            if (empty($cliente->nombre)) {
                $cliente->nombre = $cliente->telefono ?: $cliente->email;
            }
        });
    }

    /**
     * Validar RUT uruguayo
     */
    public static function validarRutUruguayo($rut)
    {
        // Remover puntos y guiones
        $rut = preg_replace('/[^0-9]/', '', $rut);
        
        // El RUT uruguayo debe tener 7-8 dígitos
        if (strlen($rut) < 7 || strlen($rut) > 8) {
            return false;
        }
        
        // Validar dígito verificador
        $digitos = str_split($rut);
        $suma = 0;
        $multiplicador = 2;
        
        for ($i = count($digitos) - 2; $i >= 0; $i--) {
            $suma += $digitos[$i] * $multiplicador;
            $multiplicador = $multiplicador == 7 ? 2 : $multiplicador + 1;
        }
        
        $resto = $suma % 11;
        $dv = 11 - $resto;
        
        if ($dv == 11) $dv = 0;
        if ($dv == 10) $dv = 'K';
        
        return $dv == $digitos[count($digitos) - 1];
    }

    /**
     * Formatear RUT uruguayo
     */
    public function getRutFormateadoAttribute()
    {
        if (!$this->rut) return '';
        
        $rut = preg_replace('/[^0-9]/', '', $this->rut);
        if (strlen($rut) < 7) return $this->rut;
        
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, -1);
        
        return number_format($numero, 0, ',', '.') . '-' . $dv;
    }

    /**
     * Define la relación: un cliente tiene muchos pedidos.
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    // Métodos para estadísticas del dashboard
    public static function getTopClientes($limit = 5, $meses = 3)
    {
        return self::withCount(['pedidos' => function($query) use ($meses) {
            $query->where('created_at', '>=', now()->subMonths($meses));
        }])
        ->orderBy('pedidos_count', 'desc')
        ->limit($limit)
        ->get();
    }

    public static function getClientesConMasGasto($limit = 5, $meses = 3)
    {
        return self::withSum(['pedidos' => function($query) use ($meses) {
            $query->where('created_at', '>=', now()->subMonths($meses))
                  ->where('estado', 'Entregado');
        }], 'costo_total_venta')
        ->orderBy('pedidos_sum_costo_total_venta', 'desc')
        ->limit($limit)
        ->get();
    }
}