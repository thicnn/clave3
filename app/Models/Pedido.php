<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\Insumo;
use App\Models\ArchivoPedido;

class Pedido extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'fecha_entrega',
        'estado',
        'total',
        'costo_total_venta',
        'costo_produccion_total',
        'motivo_cancelacion',
        'fecha_cancelacion',
    ];

    /**
     * Define la relación: un pedido pertenece a un cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Define la relación muchos a muchos con Insumo.
     */
    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)->withPivot('cantidad');
    }

    /**
     * Define la relación: un pedido tiene muchos archivos.
     */
    public function archivos()
    {
        return $this->hasMany(ArchivoPedido::class);
    }

    // Métodos para estadísticas del dashboard
    public static function getPedidosHoy()
    {
        return self::whereDate('created_at', today())->count();
    }

    public static function getVentasHoy()
    {
        return self::whereDate('created_at', today())
            ->where('estado', 'Entregado')
            ->sum('costo_total_venta');
    }

    public static function getPedidosEnCurso()
    {
        return self::where('estado', 'En Curso')->count();
    }

    public static function getPedidosListosParaRetirar()
    {
        return self::where('estado', 'Listo para Retirar')->count();
    }

    public static function getUltimosPedidos($limit = 5)
    {
        return self::with('cliente')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getPedidosPorEstado()
    {
        return self::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado');
    }

    public static function getVentasSemana()
    {
        return self::where('estado', 'Entregado')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('costo_total_venta');
    }

    public static function getVentasMes()
    {
        return self::where('estado', 'Entregado')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('costo_total_venta');
    }

    /**
     * Relación con pedido_items
     */
    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    /**
     * Verificar si todos los archivos están marcados como impresos
     */
    public function todosArchivosImpresos(): bool
    {
        if ($this->archivos->count() == 0) {
            return false;
        }

        return $this->archivos->every(function ($archivo) {
            return $archivo->impreso;
        });
    }

    /**
     * Cambiar estado automáticamente si todos los archivos están impresos
     */
    public function verificarEstadoAutomatico(): void
    {
        if ($this->estado === 'Confirmado' && $this->todosArchivosImpresos()) {
            $this->update(['estado' => 'Listo para Retirar']);
        }
    }

    /**
     * Cancelar pedido
     */
    public function cancelar(string $motivo): void
    {
        $this->update([
            'estado' => 'Cancelado',
            'motivo_cancelacion' => $motivo,
            'fecha_cancelacion' => now(),
        ]);
    }

    /**
     * Verificar si el pedido está cancelado
     */
    public function estaCancelado(): bool
    {
        return $this->estado === 'Cancelado';
    }

    /**
     * Obtener el estado con colores para la UI
     */
    public function getEstadoColorAttribute(): string
    {
        $colores = [
            'Solicitud' => 'gray',
            'Cotización' => 'yellow',
            'Confirmado' => 'blue',
            'En Curso' => 'purple',
            'Listo para Retirar' => 'orange',
            'Entregado' => 'green',
            'Cancelado' => 'red',
        ];

        return $colores[$this->estado] ?? 'gray';
    }

    /**
     * Calcular costo total de producción del pedido
     */
    public function calcularCostoProduccionTotal(): float
    {
        return $this->items->sum('costo_produccion_item');
    }

    /**
     * Calcular precio total de venta del pedido
     */
    public function calcularPrecioVentaTotal(): float
    {
        return $this->items->sum('precio_venta_item');
    }

    /**
     * Calcular ganancia del pedido
     */
    public function calcularGanancia(): float
    {
        return $this->costo_total_venta - $this->costo_produccion_total;
    }

    /**
     * Calcular margen de ganancia en porcentaje
     */
    public function calcularMargenGanancia(): float
    {
        if ($this->costo_produccion_total == 0) {
            return 0;
        }
        
        return (($this->costo_total_venta - $this->costo_produccion_total) / $this->costo_produccion_total) * 100;
    }

    /**
     * Actualizar totales del pedido
     */
    public function actualizarTotales(): void
    {
        $this->costo_produccion_total = $this->calcularCostoProduccionTotal();
        $this->costo_total_venta = $this->calcularPrecioVentaTotal();
        $this->total = $this->costo_total_venta; // Mantener compatibilidad
        $this->save();
    }

    /**
     * Descontar stock de insumos
     */
    public function descontarStock(): void
    {
        foreach ($this->items as $item) {
            if ($item->insumo_id && in_array($item->categoria, ['impresion', 'fotocopia'])) {
                $cantidadHojas = $item->calcularCantidadHojas();
                $insumo = $item->insumo;
                $insumo->stock_actual -= $cantidadHojas;
                $insumo->save();
            }
        }
    }

    /**
     * Restaurar stock de insumos
     */
    public function restaurarStock(): void
    {
        foreach ($this->items as $item) {
            if ($item->insumo_id && in_array($item->categoria, ['impresion', 'fotocopia'])) {
                $cantidadHojas = $item->calcularCantidadHojas();
                $insumo = $item->insumo;
                $insumo->stock_actual += $cantidadHojas;
                $insumo->save();
            }
        }
    }
}