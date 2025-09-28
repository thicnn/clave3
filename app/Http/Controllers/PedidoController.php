<?php

namespace App\Http\Controllers;

use App\Models\Pedido; // <-- ESTA ES LA LÍNEA QUE FALTA
use App\Models\Cliente;
use App\Models\Insumo;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Usamos with('cliente') para cargar la relación y evitar N+1 problemas
    $pedidos = Pedido::with('cliente')->latest()->get();

    return view('pedidos.index', ['pedidos' => $pedidos]);
}

    /**
     * Show the form for creating a new resource.
     */
  public function create()
{
    $clientes = Cliente::orderBy('nombre')->get();
    $insumos = Insumo::orderBy('nombre')->get(); // <-- AÑADE ESTA LÍNEA

    return view('pedidos.create', [
        'clientes' => $clientes,
        'insumos' => $insumos // <-- Y PASA LA NUEVA VARIABLE
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // 1. Validación de los datos del formulario
    $validatedData = $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'fecha_entrega' => 'nullable|date',
        'estado' => 'required|string',
        'total' => 'nullable|numeric|min:0',
    ]);

    // 2. Creación del nuevo Pedido
    Pedido::create($validatedData);

    // 3. Redirección con mensaje de éxito
    return redirect()->route('pedidos.create')->with('success', '¡Pedido registrado exitosamente!');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
{
    // También necesitamos la lista de todos los clientes
    // por si el usuario quiere cambiar el cliente del pedido.
    $clientes = Cliente::orderBy('nombre')->get();

    return view('pedidos.edit', [
        'pedido' => $pedido,
        'clientes' => $clientes
    ]);
}

    /**
 * Update the specified resource in storage.
 */
public function update(Request $request, Pedido $pedido)
{
    // 1. Validación (usamos las mismas reglas que en store)
    $validatedData = $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'fecha_entrega' => 'nullable|date',
        'estado' => 'required|string',
        'total' => 'nullable|numeric|min:0',
    ]);

    // 2. Actualización del Pedido
    $pedido->update($validatedData);

    // 3. Redirección con mensaje de éxito
    return redirect()->route('pedidos.index')->with('success', '¡Pedido #' . $pedido->id . ' actualizado correctamente!');
}

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource from storage.
 */
public function destroy(Pedido $pedido)
{
    // Usa el objeto $pedido que Laravel ya encontró por nosotros
    $pedido->delete();

    // Redirecciona a la lista con un mensaje de éxito
    return redirect()->route('pedidos.index')->with('success', '¡Pedido eliminado correctamente!');
}
}
