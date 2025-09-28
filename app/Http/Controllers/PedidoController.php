<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Insumo;
use App\Models\ArchivoPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PedidoController extends Controller
{
    /**
     * Muestra una lista de todos los pedidos.
     */
    public function index()
    {
        $pedidos = Pedido::with('cliente')->latest()->get();
        return view('pedidos.index', ['pedidos' => $pedidos]);
    }

    /**
     * Muestra el formulario para crear un nuevo pedido.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $insumos = Insumo::orderBy('nombre')->get();
        return view('pedidos.create', [
            'clientes' => $clientes,
            'insumos' => $insumos
        ]);
    }

    /**
     * Guarda un nuevo pedido en la base de datos.
     */
    public function store(Request $request)
    {
        dd($request->input('insumos'));
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_entrega' => 'nullable|date',
            'estado' => 'required|string',
            'total' => 'nullable|numeric|min:0',
            'insumos' => 'nullable|array',
            'insumos.*.cantidad' => [
                'required_with:insumos.*.id',
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $insumoId = explode('.', $attribute)[1];
                    $insumo = Insumo::find($insumoId);
                    if ($insumo && $value > $insumo->stock_actual) {
                        $fail("La cantidad para {$insumo->nombre} excede el stock disponible ({$insumo->stock_actual}).");
                    }
                },
            ],
            'archivos' => 'nullable|array',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,png,zip,rar|max:10240',
        ]);

        // Creación del Pedido
        $pedido = Pedido::create([
            'cliente_id' => $validatedData['cliente_id'],
            'fecha_entrega' => $validatedData['fecha_entrega'],
            'estado' => $validatedData['estado'],
            'total' => $validatedData['total'],
        ]);

        // Guardar los insumos
        if (isset($validatedData['insumos'])) {
            foreach ($validatedData['insumos'] as $insumoId => $insumoData) {
                if (isset($insumoData['id']) && isset($insumoData['cantidad'])) {
                    $pedido->insumos()->attach($insumoId, ['cantidad' => $insumoData['cantidad']]);
                }
            }
        }

        // Guardar los archivos
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

        return redirect()->route('pedidos.index')->with('success', '¡Pedido registrado con éxito!');
    }

    /**
     * Muestra los detalles de un pedido específico.
     */
    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'insumos', 'archivos');
        return view('pedidos.show', ['pedido' => $pedido]);
    }

    /**
     * Muestra el formulario para editar un pedido.
     */
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $insumos = Insumo::orderBy('nombre')->get();
        $pedido->load('insumos');

        return view('pedidos.edit', [
            'pedido' => $pedido,
            'clientes' => $clientes,
            'insumos' => $insumos,
        ]);
    }

    /**
     * Actualiza un pedido en la base de datos.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_entrega' => 'nullable|date',
            'estado' => 'required|string',
            'total' => 'nullable|numeric|min:0',
            'insumos' => 'nullable|array',
            'insumos.*.cantidad' => [
                'required_with:insumos.*.id',
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $insumoId = explode('.', $attribute)[1];
                    $insumo = Insumo::find($insumoId);
                    if ($insumo && $value > $insumo->stock_actual) {
                        $fail("La cantidad para {$insumo->nombre} excede el stock disponible ({$insumo->stock_actual}).");
                    }
                },
            ],
            'archivos' => 'nullable|array',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,png,zip,rar|max:10240',
        ]);

        $estadoOriginal = $pedido->getOriginal('estado');

        $pedido->update([
            'cliente_id' => $validatedData['cliente_id'],
            'fecha_entrega' => $validatedData['fecha_entrega'],
            'estado' => $validatedData['estado'],
            'total' => $validatedData['total'],
        ]);

        $insumosParaSincronizar = [];
        if (isset($validatedData['insumos'])) {
            foreach ($validatedData['insumos'] as $insumoId => $insumoData) {
                if (isset($insumoData['id']) && isset($insumoData['cantidad'])) {
                    $insumosParaSincronizar[$insumoId] = ['cantidad' => $insumoData['cantidad']];
                }
            }
        }
        $pedido->insumos()->sync($insumosParaSincronizar);

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
        
        $pedido->refresh();

        $estadosDeDescuento = ['Confirmado', 'Entregado'];
        if (!in_array($estadoOriginal, $estadosDeDescuento) && in_array($pedido->estado, $estadosDeDescuento)) {
            if ($pedido->insumos->isNotEmpty()) {
                foreach ($pedido->insumos as $insumo) {
                    $cantidadUsada = $insumo->pivot->cantidad;
                    $insumo->stock_actual -= $cantidadUsada;
                    $insumo->save();
                }
            }
        }

        return redirect()->route('pedidos.index')->with('success', '¡Pedido #' . $pedido->id . ' actualizado con éxito!');
    }

    /**
     * Elimina un pedido de la base de datos.
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return redirect()->route('pedidos.index')->with('success', '¡Pedido eliminado correctamente!');
    }

    /**
     * Elimina un archivo adjunto de un pedido.
     */
    public function destroyArchivo(Pedido $pedido, ArchivoPedido $archivo)
    {
        // 1. Verificar que el archivo pertenece al pedido (por seguridad)
        if ($archivo->pedido_id !== $pedido->id) {
            return back()->with('error', 'Acción no autorizada.');
        }

        // 2. Eliminar el archivo físico del disco
        Storage::disk('public')->delete($archivo->ruta);

        // 3. Eliminar el registro de la base de datos
        $archivo->delete();

        // 4. Redireccionar de vuelta a la página de edición con un mensaje
        return back()->with('success', '¡Archivo eliminado correctamente!');
    }
}