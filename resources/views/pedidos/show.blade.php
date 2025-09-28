@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pedido #{{ $pedido->id }}</h1>
                <p class="mt-2 text-sm text-gray-600">Detalles del pedido</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('pedidos.edit', $pedido->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('pedidos.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Información del Pedido</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pedido->cliente->nombre ?? 'Sin nombre' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($pedido->estado == 'Entregado') bg-green-100 text-green-800
                                    @elseif($pedido->estado == 'En Curso') bg-yellow-100 text-yellow-800
                                    @elseif($pedido->estado == 'Listo para Retirar') bg-purple-100 text-purple-800
                                    @elseif($pedido->estado == 'Cancelado') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $pedido->estado }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \App\Helpers\DateHelper::formatSpanishDateTime($pedido->created_at) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Entrega</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $pedido->fecha_entrega ? \App\Helpers\DateHelper::formatSpanish($pedido->fecha_entrega) : 'Sin fecha' }}
                            </dd>
                        </div>
                        @if($pedido->notas)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Notas</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pedido->notas }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Productos del Pedido</h3>
                </div>
                <div class="px-6 py-4">
                    @if($pedido->items->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pedido->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->producto->nombre ?? 'Producto eliminado' }}
                                            </div>
                                            @if($item->producto)
                                                <div class="text-sm text-gray-500">
                                                    {{ $item->producto->categoria }} - 
                                                    @if($item->producto->maquina)
                                                        {{ $item->producto->maquina->nombre }}
                                                    @endif
                                                    @if($item->producto->tipo_impresion)
                                                        - {{ $item->producto->tipo_impresion }}
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->cantidad }}
                                            @if($item->es_doble_faz)
                                                <span class="text-xs text-gray-500">(doble faz)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($item->producto->precio_venta ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($item->descuento_item > 0)
                                                {{ number_format($item->descuento_item, 1) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ number_format($item->precio_venta_item, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No hay productos en este pedido.</p>
                    @endif
                </div>
            </div>

            <!-- Archivos del Pedido -->
            @if($pedido->archivos->count() > 0)
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Archivos Adjuntos</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($pedido->archivos as $archivo)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $archivo->nombre_original }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($archivo->tamaño / 1024, 1) }} KB</p>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <a href="{{ route('pedidos.archivos.download', ['pedido' => $pedido->id, 'archivo' => $archivo->id]) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            @if($archivo->impreso)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Impreso
                                </span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Resumen Financiero -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Resumen Financiero</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Total de Venta:</span>
                        <span class="text-sm font-medium text-gray-900">${{ number_format($pedido->costo_total_venta, 2) }}</span>
                    </div>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Costo de Producción:</span>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($pedido->costo_produccion_total, 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-2">
                                <span class="text-sm font-medium text-gray-500">Ganancia:</span>
                                <span class="text-sm font-medium text-green-600">
                                    ${{ number_format($pedido->costo_total_venta - $pedido->costo_produccion_total, 2) }}
                                </span>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Cliente</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nombre:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $pedido->cliente->nombre ?? 'Sin nombre' }}</span>
                        </div>
                        @if($pedido->cliente->telefono)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Teléfono:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $pedido->cliente->telefono }}</span>
                        </div>
                        @endif
                        @if($pedido->cliente->email)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $pedido->cliente->email }}</span>
                        </div>
                        @endif
                        @if($pedido->cliente->notas)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Notas:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $pedido->cliente->notas }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Acciones del Pedido -->
            @if($pedido->estado !== 'Entregado' && $pedido->estado !== 'Cancelado')
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Acciones</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if($pedido->estado === 'Solicitud')
                        <form method="POST" action="{{ route('pedidos.cambiar-estado', $pedido->id) }}" class="w-full">
                            @csrf
                            <input type="hidden" name="estado" value="Cotización">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Convertir a Cotización
                            </button>
                        </form>
                    @elseif($pedido->estado === 'Cotización')
                        <form method="POST" action="{{ route('pedidos.cambiar-estado', $pedido->id) }}" class="w-full">
                            @csrf
                            <input type="hidden" name="estado" value="Confirmado">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Confirmar Pedido
                            </button>
                        </form>
                    @elseif($pedido->estado === 'Confirmado')
                        <form method="POST" action="{{ route('pedidos.cambiar-estado', $pedido->id) }}" class="w-full">
                            @csrf
                            <input type="hidden" name="estado" value="En Curso">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                                Iniciar Producción
                            </button>
                        </form>
                    @elseif($pedido->estado === 'En Curso')
                        <form method="POST" action="{{ route('pedidos.cambiar-estado', $pedido->id) }}" class="w-full">
                            @csrf
                            <input type="hidden" name="estado" value="Listo para Retirar">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                                Marcar Listo para Retirar
                            </button>
                        </form>
                    @elseif($pedido->estado === 'Listo para Retirar')
                        <form method="POST" action="{{ route('pedidos.cambiar-estado', $pedido->id) }}" class="w-full">
                            @csrf
                            <input type="hidden" name="estado" value="Entregado">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Marcar como Entregado
                            </button>
                        </form>
                    @endif
                    
                    @if($pedido->estado !== 'Entregado')
                        <button onclick="cancelarPedido()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                            Cancelar Pedido
                        </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para cancelar pedido -->
<div id="cancelarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cancelar Pedido</h3>
            <form method="POST" action="{{ route('pedidos.cancelar', $pedido->id) }}">
                @csrf
                <div class="mb-4">
                    <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700">Motivo de cancelación</label>
                    <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Confirmar Cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cancelarPedido() {
    document.getElementById('cancelarModal').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('cancelarModal').classList.add('hidden');
}
</script>
@endsection