<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // KPIs del día
        $pedidosHoy = Pedido::whereDate('created_at', $today)->count();
        $ventasHoy = Pedido::whereDate('created_at', $today)
            ->where('estado', 'Entregado')
            ->sum('costo_total_venta');
        
        // Cola de producción
        $pedidosEnCurso = Pedido::where('estado', 'En Curso')->count();
        $pedidosListosParaRetirar = Pedido::where('estado', 'Listo para Retirar')->count();
        
        // Últimos pedidos
        $ultimosPedidos = Pedido::with('cliente')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Top 5 clientes (últimos 3 meses)
        $topClientes = Cliente::withCount(['pedidos' => function($query) {
            $query->where('created_at', '>=', Carbon::now()->subMonths(3));
        }])
        ->orderBy('pedidos_count', 'desc')
        ->limit(5)
        ->get();
        
        // Estadísticas adicionales
        $totalPedidos = Pedido::count();
        $pedidosPendientes = Pedido::whereIn('estado', ['Solicitud', 'Cotización', 'Confirmado', 'En Curso'])->count();
        $productosActivos = Producto::where('activo', true)->count();
        $insumosBajoStock = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        
        // Pedidos por estado
        $pedidosPorEstado = Pedido::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado');
        
        // Ventas de la semana
        $ventasSemana = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('costo_total_venta');
        
        // Ventas del mes
        $ventasMes = Pedido::where('estado', 'Entregado')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('costo_total_venta');
        
        return view('dashboard.index', compact(
            'pedidosHoy',
            'ventasHoy',
            'pedidosEnCurso',
            'pedidosListosParaRetirar',
            'ultimosPedidos',
            'topClientes',
            'totalPedidos',
            'pedidosPendientes',
            'productosActivos',
            'insumosBajoStock',
            'pedidosPorEstado',
            'ventasSemana',
            'ventasMes'
        ));
    }
}
