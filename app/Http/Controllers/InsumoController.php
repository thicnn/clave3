<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    /**
     * Muestra una lista de todos los insumos.
     */
    public function index()
    {
        $insumos = Insumo::orderBy('nombre')->get(); // Obtenemos todos los insumos ordenados por nombre
        return view('insumos.index', ['insumos' => $insumos]);
    }

    /**
     * Muestra el formulario para crear un nuevo insumo.
     */
    public function create()
    {
        return view('insumos.create');
    }

    /**
     * Guarda un nuevo insumo en la base de datos.
     */
    public function store(Request $request)
{
    // Validación de los datos
    $validatedData = $request->validate([ // Guardamos los datos validados en una variable
        'nombre' => 'required|string|max:255|unique:insumos,nombre',
        'descripcion' => 'nullable|string',
        'costo' => 'required|numeric|min:0',
        'stock_actual' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
    ]);

    // Usamos solo los datos validados para la creación
    Insumo::create($validatedData);

    // Redirección con mensaje de éxito
    return redirect()->route('insumos.index')->with('success', '¡Insumo añadido al inventario!');
}

    /**
     * Muestra los detalles de un insumo específico (opcional).
     */
    public function show(Insumo $insumo)
    {
        // De momento no usaremos esta vista, pero la dejamos preparada.
    }

    /**
     * Muestra el formulario para editar un insumo.
     */
    public function edit(Insumo $insumo)
    {
        return view('insumos.edit', ['insumo' => $insumo]);
    }

    /**
     * Actualiza un insumo en la base de datos.
     */
    public function update(Request $request, Insumo $insumo)
{
    // Validación de los datos
    $validatedData = $request->validate([
        'nombre' => 'required|string|max:255|unique:insumos,nombre,' . $insumo->id,
        'descripcion' => 'nullable|string',
        'costo' => 'required|numeric|min:0',
        'costo_por_unidad' => 'required|numeric|min:0',
        'stock_actual' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
    ]);

    // Usamos solo los datos validados para la actualización
    $insumo->update($validatedData);

    // Redirección con mensaje de éxito
    return redirect()->route('insumos.index')->with('success', '¡Insumo actualizado correctamente!');
}

    /**
     * Elimina un insumo del inventario.
     */
    public function destroy(Insumo $insumo)
    {
        $insumo->delete();

        return redirect()->route('insumos.index')->with('success', '¡Insumo eliminado del inventario!');
    }
}