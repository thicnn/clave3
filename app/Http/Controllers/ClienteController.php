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
     * Guarda un nuevo cliente en la base de datos.
     */
    /**
 * Guarda un nuevo cliente en la base de datos.
 */
public function store(Request $request)
{
    // 1. Validación de los datos del formulario
    $request->validate([
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|unique:clientes,email',
        'telefono' => 'nullable|numeric|digits_between:8,9', // <-- MODIFICADO
        'rut' => 'nullable|numeric|digits:12',               // <-- MODIFICADO
        'direccion' => 'nullable|string',
    ]);

    // 2. Creación del nuevo cliente en la base de datos
    Cliente::create([
        'nombre' => $request->input('nombre'),
        'email' => $request->input('email'),
        'telefono' => $request->input('telefono'),
        'rut' => $request->input('rut'),
        'direccion' => $request->input('direccion'),
    ]);

    // 3. Redirección a la lista de clientes con un mensaje de éxito
    return redirect()->route('clientes.index')->with('success', '¡Cliente creado exitosamente!');
}

    /**
     * Muestra los detalles de un cliente específico.
     */
    public function show(string $id)
    {
        //
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
        'direccion' => 'nullable|string',
    ]);

    // 3. Actualiza los datos en la base de datos
    $cliente->update([
        'nombre' => $request->input('nombre'),
        'email' => $request->input('email'),
        'telefono' => $request->input('telefono'),
        'rut' => $request->input('rut'),
        'direccion' => $request->input('direccion'),
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