<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Insumo;
use App\Models\ArchivoPedido;
use App\Models\Maquina;
use App\Models\PedidoItem;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Muestra una lista de pedidos.
     */
    public function index()
    {
        $pedidos = Pedido::with('cliente')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Muestra los detalles de un pedido específico.
     */
    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'insumos', 'archivos', 'items.producto.maquina', 'items.producto.insumo');
        return view('pedidos.show', ['pedido' => $pedido]);
    }

    /**
     * Muestra el formulario para crear un nuevo pedido.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::activos()->with(['maquina', 'insumo'])->orderBy('categoria')->orderBy('nombre')->get();
        $maquinas = Maquina::orderBy('nombre')->get();
        
        return view('pedidos.create', [
            'clientes' => $clientes,
            'productos' => $productos,
            'maquinas' => $maquinas
        ]);
    }

    /**
     * Guarda un nuevo pedido en la base de datos.
     */
    public function store(Request $request)
    {
        // Log de debugging
        \Log::info('=== PEDIDO STORE INICIADO ===');
        \Log::info('Datos recibidos:', $request->all());
        
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_entrega' => 'nullable|date|after_or_equal:today',
            'estado' => 'required|in:Solicitud,Cotización,Confirmado,En Curso,Listo para Retirar,Entregado',
            'notas' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.descuento_item' => 'nullable|numeric|min:0|max:100',
            'items.*.es_doble_faz' => 'nullable|boolean',
            'archivos' => 'nullable|array',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,png,zip,rar|max:102400',
        ]);
        
        \Log::info('Datos validados:', $validatedData);

        try {
            DB::beginTransaction();
            \Log::info('Iniciando transacción de base de datos');

            // Crear el pedido
            $pedido = Pedido::create([
                'cliente_id' => $validatedData['cliente_id'],
                'fecha_entrega' => $validatedData['fecha_entrega'],
                'estado' => $validatedData['estado'],
                'notas' => $validatedData['notas'] ?? null,
                'total' => 0, // Se actualizará después
                'costo_total_venta' => 0, // Se actualizará después
                'costo_produccion_total' => 0, // Se actualizará después
            ]);
            
            \Log::info('Pedido creado con ID: ' . $pedido->id);

            // Crear ítems del pedido
            $totalVenta = 0;
            $totalProduccion = 0;
            
            \Log::info('Creando ítems del pedido. Total de ítems: ' . count($validatedData['items']));

            foreach ($validatedData['items'] as $index => $itemData) {
                \Log::info("Creando ítem $index:", $itemData);
                
                // Obtener el producto para calcular precios
                $producto = Producto::find($itemData['producto_id']);
                if (!$producto) {
                    throw new \Exception("Producto con ID {$itemData['producto_id']} no encontrado");
                }
                
                // Calcular precios antes de crear el ítem
                $cantidad = $itemData['cantidad'];
                $esDobleFaz = $itemData['es_doble_faz'] ?? false;
                $descuento = $itemData['descuento_item'] ?? 0;
                
                // Calcular precio base del producto
                $precioBase = $producto->precio_venta * $cantidad;
                
                // Aplicar descuento
                $montoDescuento = $precioBase * ($descuento / 100);
                $precioVentaItem = $precioBase - $montoDescuento;
                
                // Calcular costo de producción
                $costoProduccionItem = $producto->costo_produccion * $cantidad;
                
                \Log::info("Cálculos para ítem $index:", [
                    'producto_precio' => $producto->precio_venta,
                    'cantidad' => $cantidad,
                    'precio_base' => $precioBase,
                    'descuento' => $descuento,
                    'monto_descuento' => $montoDescuento,
                    'precio_venta_final' => $precioVentaItem,
                    'costo_produccion' => $costoProduccionItem
                ]);
                
                $item = PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $itemData['producto_id'],
                    'cantidad' => $cantidad,
                    'es_doble_faz' => $esDobleFaz,
                    'descuento_item' => $descuento,
                    'precio_venta_item' => $precioVentaItem,
                    'costo_produccion_item' => $costoProduccionItem,
                ]);
                
                \Log::info("Ítem $index creado con ID: " . $item->id);

                $totalVenta += $precioVentaItem;
                $totalProduccion += $costoProduccionItem;
            }
            
            \Log::info('Totales calculados:', [
                'total_venta' => $totalVenta,
                'total_produccion' => $totalProduccion
            ]);

            // Actualizar totales del pedido
            $pedido->update([
                'total' => $totalVenta,
                'costo_total_venta' => $totalVenta,
                'costo_produccion_total' => $totalProduccion,
            ]);

            // Guardar archivos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    if ($archivo) {
                        $ruta = $archivo->store('archivos_pedidos', 'public');
                        $pedido->archivos()->create([
                            'nombre_original' => $archivo->getClientOriginalName(),
                            'ruta' => $ruta,
                        ]);
                    }
                }
            }

            // Gestionar stock según el estado
            $estadosDeDescuento = ['Listo para Retirar', 'Entregado'];
            if (in_array($pedido->estado, $estadosDeDescuento) && $pedido->estado !== 'Cancelado') {
                $pedido->descontarStock();
            }

            DB::commit();
            \Log::info('Transacción completada exitosamente');

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', '¡Pedido creado correctamente!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al crear pedido: ' . $e->getMessage());
            \Log::error('Datos recibidos: ' . json_encode($request->all()));
            return back()->withErrors(['error' => 'Error al crear el pedido: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un pedido.
     */
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::activos()->with(['maquina', 'insumo'])->orderBy('categoria')->orderBy('nombre')->get();
        $maquinas = Maquina::orderBy('nombre')->get();
        
        // Cargar ítems del pedido
        $pedido->load('items.producto.maquina', 'items.producto.insumo');

        return view('pedidos.edit', [
            'pedido' => $pedido,
            'clientes' => $clientes,
            'productos' => $productos,
            'maquinas' => $maquinas
        ]);
    }

    /**
     * Actualiza un pedido en la base de datos.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_entrega' => 'nullable|date|after_or_equal:today',
            'estado' => 'required|in:Solicitud,Cotización,Confirmado,En Curso,Listo para Retirar,Entregado,Cancelado',
            'notas' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.descuento_item' => 'nullable|numeric|min:0|max:100',
            'items.*.es_doble_faz' => 'nullable|boolean',
            'archivos' => 'nullable|array',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,png,zip,rar|max:102400',
        ]);

        try {
            DB::beginTransaction();

            $estadoOriginal = $pedido->estado;

            // Actualizar información básica del pedido
            $pedido->update([
                'cliente_id' => $validatedData['cliente_id'],
                'fecha_entrega' => $validatedData['fecha_entrega'],
                'estado' => $validatedData['estado'],
                'notas' => $validatedData['notas'] ?? null,
            ]);

            // Eliminar ítems existentes
            $pedido->items()->delete();

            // Crear nuevos ítems
            $totalVenta = 0;
            $totalProduccion = 0;

            foreach ($validatedData['items'] as $itemData) {
                $item = PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $itemData['producto_id'],
                    'cantidad' => $itemData['cantidad'],
                    'es_doble_faz' => $itemData['es_doble_faz'] ?? false,
                    'descuento_item' => $itemData['descuento_item'] ?? 0,
                ]);

                // Calcular precios basados en el producto
                $item->precio_venta_item = $item->calcularPrecioVenta();
                $item->costo_produccion_item = $item->calcularCostoProduccion();
                $item->save();

                $totalVenta += $item->precio_venta_item;
                $totalProduccion += $item->costo_produccion_item;
            }

            // Actualizar totales del pedido
            $pedido->update([
                'total' => $totalVenta,
                'costo_total_venta' => $totalVenta,
                'costo_produccion_total' => $totalProduccion,
            ]);

            // Guardar archivos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    if ($archivo) {
                        $ruta = $archivo->store('archivos_pedidos', 'public');
                        $pedido->archivos()->create([
                            'nombre_original' => $archivo->getClientOriginalName(),
                            'ruta' => $ruta,
                        ]);
                    }
                }
            }

            // Gestionar stock según el cambio de estado
            $estadosDeDescuento = ['Listo para Retirar', 'Entregado'];
            $estadoOriginalDescontaba = in_array($estadoOriginal, $estadosDeDescuento);
            $nuevoEstadoDescuenta = in_array($pedido->estado, $estadosDeDescuento);

            if (!$estadoOriginalDescontaba && $nuevoEstadoDescuenta) {
                // Descontar stock
                $pedido->descontarStock();
            } elseif ($estadoOriginalDescontaba && !$nuevoEstadoDescuenta) {
                // Restaurar stock
                $pedido->restaurarStock();
            }

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', '¡Pedido actualizado correctamente!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el pedido: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Elimina un pedido de la base de datos.
     */
    public function destroy(Pedido $pedido)
    {
        try {
            DB::beginTransaction();

            // 1. Restaurar stock si el pedido tenía insumos descontados
            $estadosDeDescuento = ['Listo para Retirar', 'Entregado'];
            if (in_array($pedido->estado, $estadosDeDescuento)) {
                foreach ($pedido->insumos as $insumo) {
                    $cantidadUsada = $insumo->pivot->cantidad;
                    $insumo->stock_actual += $cantidadUsada;
                    $insumo->save();
                }
            }

            // 2. Eliminar archivos físicos
            foreach ($pedido->archivos as $archivo) {
                if (Storage::disk('public')->exists($archivo->ruta)) {
                    Storage::disk('public')->delete($archivo->ruta);
                }
            }

            // 3. Eliminar el pedido (las relaciones se eliminan por cascade)
            $pedido->delete();

            DB::commit();

            return redirect()->route('pedidos.index')
                ->with('success', '¡Pedido eliminado correctamente!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pedidos.index')
                ->with('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Cancela un pedido.
     */
    public function cancelar(Request $request, Pedido $pedido)
    {
        $validatedData = $request->validate([
            'motivo_cancelacion' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $pedido->cancelar($validatedData['motivo_cancelacion']);

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', '¡Pedido cancelado correctamente!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al cancelar el pedido: ' . $e->getMessage()]);
        }
    }

    /**
     * Cambia el estado de un pedido.
     */
    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $validatedData = $request->validate([
            'estado' => 'required|string|in:Solicitud,Cotización,Confirmado,En Curso,Listo para Retirar,Entregado,Cancelado',
        ]);

        try {
            DB::beginTransaction();

            $pedido->cambiarEstado($validatedData['estado']);

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', '¡Estado del pedido actualizado correctamente!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al cambiar el estado: ' . $e->getMessage()]);
        }
    }
}