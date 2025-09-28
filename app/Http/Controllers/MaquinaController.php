<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maquinas = Maquina::orderBy('nombre')->get();
        return view('maquinas.index', compact('maquinas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('maquinas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:maquinas,nombre',
            'costo_bn_carilla' => 'required|numeric|min:0',
            'costo_color_carilla' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
        ]);

        Maquina::create($request->all());

        return redirect()->route('maquinas.index')
            ->with('success', 'Máquina creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Maquina $maquina)
    {
        return view('maquinas.show', compact('maquina'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maquina $maquina)
    {
        return view('maquinas.edit', compact('maquina'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maquina $maquina)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:maquinas,nombre,' . $maquina->id,
            'costo_bn_carilla' => 'required|numeric|min:0',
            'costo_color_carilla' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activa' => 'boolean',
        ]);

        $maquina->update($request->all());

        return redirect()->route('maquinas.index')
            ->with('success', 'Máquina actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maquina $maquina)
    {
        $maquina->delete();

        return redirect()->route('maquinas.index')
            ->with('success', 'Máquina eliminada correctamente');
    }
}