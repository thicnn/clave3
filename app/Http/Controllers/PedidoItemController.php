<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Services\PrecioCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoItemController extends Controller
{
    protected $precioCalculator;

    public function __construct(PrecioCalculatorService $precioCalculator)
    {
        $this->precioCalculator = $precioCalculator;
    }

    /**
     * Mostrar formulario para agregar ítem
     */
    public function create(Pedido $pedido)
    {
        $maquinas = $this->precioCalculator->getMaquinasDisponibles();
        $insumos = $this->precioCalculator->getInsumosDisponibles();

        return view('pedidos.items.create', compact('pedido', 'maquinas', 'insumos'));
    }

    /**
     * Guardar nuevo ítem
     */
    public function store(Request $request, Pedido $pedido)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['pedido_id'] = $pedido->id;

            // Validar datos
            $errores = $this->precioCalculator->validarDatos($data);
            if (!empty($errores)) {
                return back()->withErrors(['error' => implode(', ', $errores)])->withInput();
            }

            // Calcular precios
            $calculos = $this->precioCalculator->calcularItem($data);

            // Crear ítem
            $item = PedidoItem::create([
                'pedido_id' => $pedido->id,
                'categoria' => $data['categoria'],
                'maquina_id' => $data['maquina_id'] ?? null,
                'insumo_id' => $data['insumo_id'] ?? null,
                'tipo_impresion' => $data['tipo_impresion'] ?? null,
                'descripcion_servicio' => $data['descripcion_servicio'] ?? null,
                'cantidad' => $data['cantidad'],
                'es_doble_faz' => $data['es_doble_faz'] ?? false,
                'descuento_item' => $data['descuento_item'] ?? 0,
                'precio_venta_item' => $calculos['precio_venta_item'],
                'costo_produccion_item' => $calculos['costo_produccion_item'],
            ]);

            // Actualizar totales del pedido
            $pedido->actualizarTotales();

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', 'Ítem agregado correctamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear el ítem: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Pedido $pedido, PedidoItem $item)
    {
        $maquinas = $this->precioCalculator->getMaquinasDisponibles();
        $insumos = $this->precioCalculator->getInsumosDisponibles();

        return view('pedidos.items.edit', compact('pedido', 'item', 'maquinas', 'insumos'));
    }

    /**
     * Actualizar ítem
     */
    public function update(Request $request, Pedido $pedido, PedidoItem $item)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            // Validar datos
            $errores = $this->precioCalculator->validarDatos($data);
            if (!empty($errores)) {
                return back()->withErrors(['error' => implode(', ', $errores)])->withInput();
            }

            // Calcular precios
            $calculos = $this->precioCalculator->calcularItem($data);

            // Actualizar ítem
            $item->update([
                'categoria' => $data['categoria'],
                'maquina_id' => $data['maquina_id'] ?? null,
                'insumo_id' => $data['insumo_id'] ?? null,
                'tipo_impresion' => $data['tipo_impresion'] ?? null,
                'descripcion_servicio' => $data['descripcion_servicio'] ?? null,
                'cantidad' => $data['cantidad'],
                'es_doble_faz' => $data['es_doble_faz'] ?? false,
                'descuento_item' => $data['descuento_item'] ?? 0,
                'precio_venta_item' => $calculos['precio_venta_item'],
                'costo_produccion_item' => $calculos['costo_produccion_item'],
            ]);

            // Actualizar totales del pedido
            $pedido->actualizarTotales();

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', 'Ítem actualizado correctamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el ítem: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Eliminar ítem
     */
    public function destroy(Pedido $pedido, PedidoItem $item)
    {
        try {
            DB::beginTransaction();

            $item->delete();

            // Actualizar totales del pedido
            $pedido->actualizarTotales();

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', 'Ítem eliminado correctamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al eliminar el ítem: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener máquinas por AJAX
     */
    public function getMaquinas(Request $request)
    {
        $tipoImpresion = $request->get('tipo_impresion');
        $maquinas = $this->precioCalculator->getMaquinasDisponibles($tipoImpresion);
        
        return response()->json($maquinas);
    }

    /**
     * Calcular precio por AJAX
     */
    public function calcularPrecio(Request $request)
    {
        try {
            $data = $request->all();
            $calculos = $this->precioCalculator->calcularItem($data);
            
            return response()->json([
                'success' => true,
                'data' => $calculos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}