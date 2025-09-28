<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal de reportes
     */
    public function dashboard(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());
        
        // Convertir strings a Carbon si es necesario
        if (is_string($fechaInicio)) {
            $fechaInicio = Carbon::parse($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = Carbon::parse($fechaFin);
        }

        // KPIs de ganancia
        $ingresos = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->sum('costo_total_venta');
            
        $costos = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->sum('costo_produccion_total');
            
        $ganancia = $ingresos - $costos;

        // Evolución de ventas
        $ventasPorDia = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(costo_total_venta) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Evolución de ganancias
        $gananciasPorDia = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->select(DB::raw('DATE(created_at) as fecha'), 
                    DB::raw('SUM(costo_total_venta) as ingresos'),
                    DB::raw('SUM(costo_produccion_total) as costos'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(function($item) {
                $item->ganancia = $item->ingresos - $item->costos;
                return $item;
            });

        // Pedidos por estado
        $pedidosPorEstado = Pedido::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // Top 10 productos más rentables
        $productosRentables = PedidoItem::with('producto')
            ->whereHas('pedido', function($query) use ($fechaInicio, $fechaFin) {
                $query->where('estado', 'Entregado')
                      ->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            })
            ->select('producto_id', 
                    DB::raw('SUM(precio_venta_item) as ingresos'),
                    DB::raw('SUM(costo_produccion_item) as costos'),
                    DB::raw('SUM(cantidad) as cantidad_vendida'))
            ->groupBy('producto_id')
            ->get()
            ->map(function($item) {
                $item->ganancia = $item->ingresos - $item->costos;
                $item->margen = $item->ingresos > 0 ? ($item->ganancia / $item->ingresos) * 100 : 0;
                return $item;
            })
            ->sortByDesc('ganancia')
            ->take(10);

        return view('reports.dashboard', compact(
            'fechaInicio',
            'fechaFin',
            'ingresos',
            'costos',
            'ganancia',
            'ventasPorDia',
            'gananciasPorDia',
            'pedidosPorEstado',
            'productosRentables'
        ));
    }

    /**
     * Reporte de ventas detallado
     */
    public function ventas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());
        
        if (is_string($fechaInicio)) {
            $fechaInicio = Carbon::parse($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = Carbon::parse($fechaFin);
        }

        $ventas = Pedido::with(['cliente', 'items.producto'])
            ->where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalVentas = $ventas->sum('costo_total_venta');
        $totalCostos = $ventas->sum('costo_produccion_total');
        $totalGanancia = $totalVentas - $totalCostos;

        return view('reports.ventas', compact(
            'ventas',
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'totalCostos',
            'totalGanancia'
        ));
    }

    /**
     * Reporte de producción semanal
     */
    public function produccionSemanal(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfWeek());
        
        if (is_string($fechaInicio)) {
            $fechaInicio = Carbon::parse($fechaInicio);
        }
        
        $fechaFin = $fechaInicio->copy()->endOfWeek();

        $produccion = [];
        for ($i = 0; $i < 7; $i++) {
            $fecha = $fechaInicio->copy()->addDays($i);
            
            $pedidos = Pedido::whereDate('created_at', $fecha)->get();
            
            $produccion[] = [
                'fecha' => $fecha,
                'dia' => $fecha->format('l'),
                'pedidos' => $pedidos->count(),
                'ventas' => $pedidos->where('estado', 'Entregado')->sum('costo_total_venta'),
                'costos' => $pedidos->where('estado', 'Entregado')->sum('costo_produccion_total'),
                'ganancia' => $pedidos->where('estado', 'Entregado')->sum('costo_total_venta') - 
                             $pedidos->where('estado', 'Entregado')->sum('costo_produccion_total')
            ];
        }

        // Calcular contadores de máquinas
        $pedidosEntregados = Pedido::whereIn('estado', ['Listo para Retirar', 'Entregado'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with(['items.producto.maquina'])
            ->get();

        $contadores = [
            'bh227' => [
                'nombre' => 'BH227',
                'meta' => 2000,
                'actual' => 0,
                'restante' => 2000
            ],
            'c454_color' => [
                'nombre' => 'C454 Color',
                'meta' => 500,
                'actual' => 0,
                'restante' => 500
            ],
            'c454_bn' => [
                'nombre' => 'C454 Blanco y Negro',
                'meta' => 950,
                'actual' => 0,
                'restante' => 950
            ]
        ];

        // Calcular contadores basados en pedidos entregados
        foreach ($pedidosEntregados as $pedido) {
            foreach ($pedido->items as $item) {
                if ($item->producto && $item->producto->maquina) {
                    $maquina = $item->producto->maquina;
                    $cantidad = $item->cantidad;

                    if ($maquina->nombre === 'BH227') {
                        $contadores['bh227']['actual'] += $cantidad;
                    } elseif ($maquina->nombre === 'C454') {
                        if ($item->producto->tipo_impresion === 'color') {
                            $contadores['c454_color']['actual'] += $cantidad;
                        } else {
                            $contadores['c454_bn']['actual'] += $cantidad;
                        }
                    }
                }
            }
        }

        // Calcular restante
        foreach ($contadores as $key => $contador) {
            $contadores[$key]['restante'] = max(0, $contador['meta'] - $contador['actual']);
        }

        return view('reports.produccion-semanal', compact('produccion', 'fechaInicio', 'fechaFin', 'contadores'));
    }

    /**
     * Comparación de ventas semanales
     */
    public function comparacionSemanal(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfWeek());
        
        if (is_string($fechaInicio)) {
            $fechaInicio = Carbon::parse($fechaInicio);
        }
        
        $semanaActual = [
            'inicio' => $fechaInicio,
            'fin' => $fechaInicio->copy()->endOfWeek()
        ];
        
        $semanaAnterior = [
            'inicio' => $fechaInicio->copy()->subWeek()->startOfWeek(),
            'fin' => $fechaInicio->copy()->subWeek()->endOfWeek()
        ];

        // Datos de la semana actual
        $ventasActual = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$semanaActual['inicio'], $semanaActual['fin']])
            ->sum('costo_total_venta');
            
        $pedidosActual = Pedido::whereBetween('created_at', [$semanaActual['inicio'], $semanaActual['fin']])
            ->count();

        // Datos de la semana anterior
        $ventasAnterior = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$semanaAnterior['inicio'], $semanaAnterior['fin']])
            ->sum('costo_total_venta');
            
        $pedidosAnterior = Pedido::whereBetween('created_at', [$semanaAnterior['inicio'], $semanaAnterior['fin']])
            ->count();

        return view('reports.comparacion-semanal', compact(
            'semanaActual',
            'semanaAnterior',
            'ventasActual',
            'ventasAnterior',
            'pedidosActual',
            'pedidosAnterior'
        ));
    }

    /**
     * Reporte de productos
     */
    public function productos(Request $request)
    {
        $busqueda = $request->get('busqueda');
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());
        
        if (is_string($fechaInicio)) {
            $fechaInicio = Carbon::parse($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = Carbon::parse($fechaFin);
        }

        // Búsqueda de pedidos por producto
        $pedidosQuery = Pedido::with(['cliente', 'items.producto'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        if ($busqueda) {
            $pedidosQuery->whereHas('items.producto', function($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            });
        }

        $pedidos = $pedidosQuery->orderBy('created_at', 'desc')->paginate(20);

        // Ranking de productos más vendidos
        $productosMasVendidos = PedidoItem::with('producto')
            ->whereHas('pedido', function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            })
            ->select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'desc')
            ->take(10)
            ->get();

        // Ranking de productos menos vendidos
        $productosMenosVendidos = PedidoItem::with('producto')
            ->whereHas('pedido', function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            })
            ->select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'asc')
            ->take(10)
            ->get();

        return view('reports.productos', compact(
            'pedidos',
            'busqueda',
            'fechaInicio',
            'fechaFin',
            'productosMasVendidos',
            'productosMenosVendidos'
        ));
    }

    /**
     * Reporte de clientes
     */
    public function clientes(Request $request)
    {
        $clienteId = $request->get('cliente_id');
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());
        
        if (is_string($fechaInicio)) {
            $fechaInicio = Carbon::parse($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = Carbon::parse($fechaFin);
        }

        $cliente = null;
        $productosCliente = collect();

        if ($clienteId) {
            $cliente = Cliente::find($clienteId);
            
            if ($cliente) {
                $productosCliente = PedidoItem::with('producto')
                    ->whereHas('pedido', function($query) use ($clienteId, $fechaInicio, $fechaFin) {
                        $query->where('cliente_id', $clienteId)
                              ->whereBetween('created_at', [$fechaInicio, $fechaFin]);
                    })
                    ->select('producto_id', DB::raw('SUM(cantidad) as total_comprado'))
                    ->groupBy('producto_id')
                    ->orderBy('total_comprado', 'desc')
                    ->get();
            }
        }

        // Top clientes por gasto
        $topClientes = Cliente::withSum(['pedidos' => function($query) use ($fechaInicio, $fechaFin) {
            $query->where('estado', 'Entregado')
                  ->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }], 'costo_total_venta')
        ->orderBy('pedidos_sum_costo_total_venta', 'desc')
        ->take(10)
        ->get();

        $clientes = Cliente::orderBy('nombre')->get();

        return view('reports.clientes', compact(
            'cliente',
            'productosCliente',
            'topClientes',
            'clientes',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Herramienta de conciliación
     */
    public function conciliacion(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());
        
        if (is_string($fechaInicio)) {
            $fechaInicio = Carbon::parse($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = Carbon::parse($fechaFin);
        }

        // Calcular dinero esperado en caja y banco
        $pagosEfectivo = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'efectivo')
            ->sum('costo_total_venta');

        $pagosBanco = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'banco')
            ->sum('costo_total_venta');

        $totalEsperado = $pagosEfectivo + $pagosBanco;

        return view('reports.conciliacion', compact(
            'fechaInicio',
            'fechaFin',
            'pagosEfectivo',
            'pagosBanco',
            'totalEsperado'
        ));
    }
}
