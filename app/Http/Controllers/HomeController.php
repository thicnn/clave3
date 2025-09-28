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

        // 4. Pasar los datos a la vista
        return view('home', [
            'pedidosRecientes' => $pedidosRecientes,
            'pedidosParaEntregar' => $pedidosParaEntregar,
            'alertasStock' => $alertasStock,
        ]);
    }
}
