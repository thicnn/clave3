<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Insumo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // 1. Obtener los últimos 5 pedidos
        $pedidosRecientes = Pedido::with('cliente')->latest()->take(5)->get();

        // 2. Obtener los pedidos listos para retirar
        $pedidosParaEntregar = Pedido::with('cliente')->where('estado', 'Listo para Retirar')->get();

        // 3. Obtener los insumos con bajo stock
        $alertasStock = Insumo::where('stock_actual', '<=', DB::raw('stock_minimo'))->get();

        // 4. Estadísticas del mes actual
        $mesActual = now()->format('Y-m');
        $ventasMesActual = Pedido::where('created_at', '>=', now()->startOfMonth())
            ->where('estado', '!=', 'Cancelado')
            ->sum('total');
        
        $metaVentasMensual = 50000; // Meta de $50,000 mensuales
        $progresoVentas = $metaVentasMensual > 0 ? ($ventasMesActual / $metaVentasMensual) * 100 : 0;

        // 5. Estadísticas de pedidos por estado
        $pedidosPorEstado = Pedido::selectRaw('estado, COUNT(*) as cantidad')
            ->groupBy('estado')
            ->get()
            ->pluck('cantidad', 'estado');

        // 6. Top 5 clientes por ventas
        $topClientes = Pedido::join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->selectRaw('clientes.nombre, SUM(pedidos.total) as total_ventas')
            ->where('pedidos.estado', '!=', 'Cancelado')
            ->groupBy('clientes.id', 'clientes.nombre')
            ->orderBy('total_ventas', 'desc')
            ->limit(5)
            ->get();

        // 7. Insumos más utilizados
        $insumosMasUsados = Insumo::join('insumo_pedido', 'insumos.id', '=', 'insumo_pedido.insumo_id')
            ->join('pedidos', 'insumo_pedido.pedido_id', '=', 'pedidos.id')
            ->selectRaw('insumos.nombre, SUM(insumo_pedido.cantidad) as total_usado')
            ->where('pedidos.estado', '!=', 'Cancelado')
            ->groupBy('insumos.id', 'insumos.nombre')
            ->orderBy('total_usado', 'desc')
            ->limit(5)
            ->get();

        // 8. Estadísticas del día
        $pedidosHoy = Pedido::whereDate('created_at', today())->count();
        $ventasHoy = Pedido::whereDate('created_at', today())
            ->where('estado', '!=', 'Cancelado')
            ->sum('total');

        // 9. Pasar los datos a la vista
        return view('home', [
            'pedidosRecientes' => $pedidosRecientes,
            'pedidosParaEntregar' => $pedidosParaEntregar,
            'alertasStock' => $alertasStock,
            'ventasMesActual' => $ventasMesActual,
            'metaVentasMensual' => $metaVentasMensual,
            'progresoVentas' => $progresoVentas,
            'pedidosPorEstado' => $pedidosPorEstado,
            'topClientes' => $topClientes,
            'insumosMasUsados' => $insumosMasUsados,
            'pedidosHoy' => $pedidosHoy,
            'ventasHoy' => $ventasHoy,
        ]);
    }
}
