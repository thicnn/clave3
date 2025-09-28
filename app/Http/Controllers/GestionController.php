<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class GestionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Gestión de usuarios
     */
    public function usuarios()
    {
        $usuarios = User::orderBy('name')->paginate(15);
        return view('gestion.usuarios', compact('usuarios'));
    }

    public function crearUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,empleado',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('gestion.usuarios')->with('success', 'Usuario creado exitosamente.');
    }

    public function actualizarUsuario(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'role' => 'required|in:admin,empleado',
        ]);

        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $usuario->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('gestion.usuarios')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function eliminarUsuario(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('gestion.usuarios')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();
        return redirect()->route('gestion.usuarios')->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Gestión de pedidos cancelados
     */
    public function pedidosCancelados()
    {
        $pedidosCancelados = Pedido::where('estado', 'Cancelado')
            ->with('cliente')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('gestion.pedidos-cancelados', compact('pedidosCancelados'));
    }

    public function eliminarPedidoCancelado(Pedido $pedido)
    {
        if ($pedido->estado !== 'Cancelado') {
            return redirect()->route('gestion.pedidos-cancelados')->with('error', 'Solo se pueden eliminar pedidos cancelados.');
        }

        // Restaurar stock si es necesario
        foreach ($pedido->items as $item) {
            if ($item->producto && $item->producto->insumo) {
                $item->producto->insumo->increment('stock_actual', $item->cantidad);
            }
        }

        $pedido->delete();
        return redirect()->route('gestion.pedidos-cancelados')->with('success', 'Pedido cancelado eliminado exitosamente.');
    }

    /**
     * Gestión de contadores de máquinas
     */
    public function contadores()
    {
        // Obtener pedidos entregados para calcular contadores
        $pedidosEntregados = Pedido::where('estado', 'Entregado')
            ->with(['items.producto.maquina'])
            ->get();

        $contadores = [
            'bh227' => [
                'nombre' => 'BH227',
                'meta' => 2000,
                'actual' => 0,
                'restante' => 2000
            ],
            'c454_color' => [
                'nombre' => 'C454 Color',
                'meta' => 500,
                'actual' => 0,
                'restante' => 500
            ],
            'c454_bn' => [
                'nombre' => 'C454 Blanco y Negro',
                'meta' => 950,
                'actual' => 0,
                'restante' => 950
            ]
        ];

        // Calcular contadores basados en pedidos entregados
        foreach ($pedidosEntregados as $pedido) {
            foreach ($pedido->items as $item) {
                if ($item->producto && $item->producto->maquina) {
                    $maquina = $item->producto->maquina;
                    $cantidad = $item->cantidad;

                    if ($maquina->nombre === 'BH227') {
                        $contadores['bh227']['actual'] += $cantidad;
                    } elseif ($maquina->nombre === 'C454') {
                        if ($item->producto->tipo_impresion === 'color') {
                            $contadores['c454_color']['actual'] += $cantidad;
                        } else {
                            $contadores['c454_bn']['actual'] += $cantidad;
                        }
                    }
                }
            }
        }

        // Calcular restante
        foreach ($contadores as $key => $contador) {
            $contadores[$key]['restante'] = max(0, $contador['meta'] - $contador['actual']);
        }

        return view('gestion.contadores', compact('contadores'));
    }
}
