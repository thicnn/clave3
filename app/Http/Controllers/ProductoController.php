<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Maquina;
use App\Models\Insumo;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::with(['maquina', 'insumo'])
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->paginate(15);
        
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $maquinas = Maquina::activas()->get();
        $insumos = Insumo::where('stock_actual', '>', 0)->orderBy('nombre')->get();
        
        return view('productos.create', compact('maquinas', 'insumos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|in:servicio,impresion,fotocopia',
            'maquina_id' => 'nullable|exists:maquinas,id',
            'tipo_impresion' => 'nullable|in:b&n,color',
            'insumo_id' => 'nullable|exists:insumos,id',
            'precio_venta' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        // Validaciones condicionales
        if ($validatedData['categoria'] === 'servicio') {
            // Para servicios, no se requiere máquina, tipo ni insumo
            $validatedData['maquina_id'] = null;
            $validatedData['tipo_impresion'] = null;
            $validatedData['insumo_id'] = null;
        } else {
            // Para impresión/fotocopia, se requiere máquina, tipo e insumo
            if (!$validatedData['maquina_id'] || !$validatedData['tipo_impresion'] || !$validatedData['insumo_id']) {
                return back()->withErrors([
                    'error' => 'Para productos de impresión/fotocopia se requiere máquina, tipo de impresión e insumo.'
                ])->withInput();
            }
        }

        // Calcular costo de producción antes de crear
        $costoProduccion = 0;
        if ($validatedData['categoria'] !== 'servicio' && $validatedData['maquina_id'] && $validatedData['insumo_id']) {
            $maquina = Maquina::find($validatedData['maquina_id']);
            $insumo = Insumo::find($validatedData['insumo_id']);
            
            if ($maquina && $insumo) {
                $costoInsumo = $insumo->costo_por_unidad;
                $costoImpresion = $validatedData['tipo_impresion'] === 'color' 
                    ? $maquina->costo_color_carilla 
                    : $maquina->costo_bn_carilla;
                $costoProduccion = $costoInsumo + $costoImpresion;
            }
        }

        // Crear el producto con costo de producción calculado
        $validatedData['costo_produccion'] = $costoProduccion;
        $producto = Producto::create($validatedData);

        return redirect()->route('productos.index')
            ->with('success', '¡Producto creado correctamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load(['maquina', 'insumo']);
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $maquinas = Maquina::activas()->get();
        $insumos = Insumo::where('stock_actual', '>', 0)->orderBy('nombre')->get();
        
        return view('productos.edit', compact('producto', 'maquinas', 'insumos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|in:servicio,impresion,fotocopia',
            'maquina_id' => 'nullable|exists:maquinas,id',
            'tipo_impresion' => 'nullable|in:b&n,color',
            'insumo_id' => 'nullable|exists:insumos,id',
            'precio_venta' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        // Validaciones condicionales
        if ($validatedData['categoria'] === 'servicio') {
            $validatedData['maquina_id'] = null;
            $validatedData['tipo_impresion'] = null;
            $validatedData['insumo_id'] = null;
        } else {
            if (!$validatedData['maquina_id'] || !$validatedData['tipo_impresion'] || !$validatedData['insumo_id']) {
                return back()->withErrors([
                    'error' => 'Para productos de impresión/fotocopia se requiere máquina, tipo de impresión e insumo.'
                ])->withInput();
            }
        }

        // Calcular costo de producción antes de actualizar
        $costoProduccion = 0;
        if ($validatedData['categoria'] !== 'servicio' && $validatedData['maquina_id'] && $validatedData['insumo_id']) {
            $maquina = Maquina::find($validatedData['maquina_id']);
            $insumo = Insumo::find($validatedData['insumo_id']);
            
            if ($maquina && $insumo) {
                $costoInsumo = $insumo->costo_por_unidad;
                $costoImpresion = $validatedData['tipo_impresion'] === 'color' 
                    ? $maquina->costo_color_carilla 
                    : $maquina->costo_bn_carilla;
                $costoProduccion = $costoInsumo + $costoImpresion;
            }
        }

        $validatedData['costo_produccion'] = $costoProduccion;
        $producto->update($validatedData);

        return redirect()->route('productos.index')
            ->with('success', '¡Producto actualizado correctamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        try {
            $producto->delete();
            return redirect()->route('productos.index')
                ->with('success', '¡Producto eliminado correctamente!');
        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos por categoría (para AJAX)
     */
    public function getByCategoria(Request $request)
    {
        $categoria = $request->input('categoria');
        $productos = Producto::activos()
            ->porCategoria($categoria)
            ->with(['maquina', 'insumo'])
            ->get();
        
        return response()->json($productos);
    }
}