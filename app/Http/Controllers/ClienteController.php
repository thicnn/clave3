<?php

namespace App\Http\Controllers;

use App\Models\Cliente; // <-- MUY IMPORTANTE: Añade esta línea para poder usar tu modelo.
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de todos los clientes.
     */
    public function index()
    {
        // Obtenemos todos los clientes de la base de datos
        $clientes = Cliente::all();

        // Devolvemos la vista 'clientes.index' y le pasamos la variable 'clientes'
        return view('clientes.index', ['clientes' => $clientes]);
    }

    /**
     * Muestra el formulario para crear un nuevo cliente.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Muestra la ficha detallada de un cliente con historial y estadísticas.
     */
    public function show(Cliente $cliente)
    {
        // Cargar pedidos con información relacionada
        $pedidos = $cliente->pedidos()->with('insumos')->latest()->paginate(10);
        
        // Calcular estadísticas
        $totalPedidos = $cliente->pedidos()->count();
        $totalGastado = $cliente->pedidos()->sum('total');
        $promedioPedido = $totalPedidos > 0 ? $totalGastado / $totalPedidos : 0;
        
        // Pedidos por estado
        $pedidosPorEstado = $cliente->pedidos()
            ->selectRaw('estado, COUNT(*) as cantidad')
            ->groupBy('estado')
            ->get()
            ->pluck('cantidad', 'estado');
        
        // Último pedido
        $ultimoPedido = $cliente->pedidos()->latest()->first();
        
        // Insumos más utilizados por este cliente
        $insumosMasUsados = $cliente->pedidos()
            ->join('insumo_pedido', 'pedidos.id', '=', 'insumo_pedido.pedido_id')
            ->join('insumos', 'insumo_pedido.insumo_id', '=', 'insumos.id')
            ->selectRaw('insumos.nombre, SUM(insumo_pedido.cantidad) as total_usado')
            ->groupBy('insumos.id', 'insumos.nombre')
            ->orderBy('total_usado', 'desc')
            ->limit(5)
            ->get();

        return view('clientes.show', compact(
            'cliente', 
            'pedidos', 
            'totalPedidos', 
            'totalGastado', 
            'promedioPedido', 
            'pedidosPorEstado', 
            'ultimoPedido', 
            'insumosMasUsados'
        ));
    }

    /**
     * Buscar clientes para autocompletado
     */
    public function buscar(Request $request)
    {
        $query = $request->input('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $clientes = Cliente::where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('telefono', 'like', "%{$query}%")
                  ->orWhere('rut', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'nombre', 'email', 'telefono', 'rut']);

        return response()->json($clientes);
    }

    /**
     * Guarda un nuevo cliente en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos del formulario
        $validatedData = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clientes,email',
            'telefono' => 'nullable|string|max:20',
            'rut' => 'nullable|string|max:20|unique:clientes,rut',
            'notas' => 'nullable|string|max:1000',
        ]);

        // Validar que al menos tenga email o teléfono
        if (empty($validatedData['email']) && empty($validatedData['telefono'])) {
            return back()->withErrors([
                'email' => 'Debe proporcionar al menos un email o teléfono.',
                'telefono' => 'Debe proporcionar al menos un email o teléfono.'
            ])->withInput();
        }

        // Validar RUT uruguayo si se proporciona
        if (!empty($validatedData['rut']) && !Cliente::validarRutUruguayo($validatedData['rut'])) {
            return back()->withErrors([
                'rut' => 'El RUT no es válido para Uruguay.'
            ])->withInput();
        }

        // 2. Creación del nuevo cliente en la base de datos
        $cliente = Cliente::create($validatedData);

        // 3. Redirección a la lista de clientes con un mensaje de éxito
        return redirect()->route('clientes.index')->with('success', '¡Cliente creado exitosamente!');
    }

    /**
     * Muestra el formulario para editar un cliente existente.
     */
    public function edit(string $id)
    {
        // Busca el cliente por su ID. Si no lo encuentra, mostrará un error 404.
        $cliente = Cliente::findOrFail($id);

        // Muestra la vista de edición y le pasa los datos del cliente encontrado.
        return view('clientes.edit', ['cliente' => $cliente]);
    }

    /**
     * Actualiza un cliente existente en la base de datos.
     */
    public function update(Request $request, string $id)
    {
        // 1. Busca el cliente que queremos actualizar
        $cliente = Cliente::findOrFail($id);

        // 2. Validación de los datos (con una pequeña diferencia en 'email')
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'telefono' => 'nullable|numeric|digits_between:8,9',
            'rut' => 'nullable|numeric|digits:12',
            'notas' => 'nullable|string|max:1000',
        ]);

        // 3. Actualiza los datos en la base de datos
        $cliente->update([
            'nombre' => $request->input('nombre'),
            'email' => $request->input('email'),
            'telefono' => $request->input('telefono'),
            'rut' => $request->input('rut'),
            'notas' => $request->input('notas'),
        ]);

        // 4. Redirecciona a la lista de clientes con un mensaje de éxito
        return redirect()->route('clientes.index')->with('success', '¡Cliente actualizado exitosamente!');
    }

    /**
     * Elimina un cliente de la base de datos.
     */
    public function destroy(string $id)
    {
        // 1. Busca el cliente que queremos eliminar
        $cliente = Cliente::findOrFail($id);

        // 2. Ejecuta el borrado
        $cliente->delete();

        // 3. Redirecciona a la lista con un mensaje de éxito
        return redirect()->route('clientes.index')->with('success', '¡Cliente eliminado exitosamente!');
    }
}