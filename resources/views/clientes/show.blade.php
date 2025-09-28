@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $cliente->nombre }}</h1>
                <p class="mt-2 text-sm text-gray-600">Ficha detallada del cliente</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('clientes.edit', $cliente->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('clientes.index') }}" 
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
        <!-- Información del Cliente -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Información del Cliente</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cliente->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cliente->telefono ?? 'No especificado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">RUT</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cliente->rut ?? 'No especificado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cliente->direccion ?? 'No especificada' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="mt-6 bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Estadísticas</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total de Pedidos</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $totalPedidos }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Gastado</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">${{ number_format($totalGastado, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Promedio por Pedido</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">${{ number_format($promedioPedido, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Insumos Más Usados -->
            @if($insumosMasUsados->count() > 0)
                <div class="mt-6 bg-white shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Insumos Más Usados</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            @foreach($insumosMasUsados as $insumo)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-900">{{ $insumo->nombre }}</span>
                                    <span class="text-sm font-medium text-gray-500">{{ $insumo->total_usado }} unidades</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Historial de Pedidos -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Historial de Pedidos</h3>
                </div>
                <div class="px-6 py-4">
                    @if($pedidos->count() > 0)
                        <div class="space-y-4">
                            @foreach($pedidos as $pedido)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Pedido #{{ $pedido->id }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $pedido->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                            <div>
                                                @php
                                                    $statusColors = [
                                                        'Cotización' => 'bg-yellow-100 text-yellow-800',
                                                        'Confirmado' => 'bg-blue-100 text-blue-800',
                                                        'Listo para Retirar' => 'bg-orange-100 text-orange-800',
                                                        'Entregado' => 'bg-green-100 text-green-800'
                                                    ];
                                                    $statusColor = $statusColors[$pedido->estado] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                                    {{ $pedido->estado }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-600">
                                            Total: ${{ number_format($pedido->total, 2) }}
                                            @if($pedido->fecha_entrega)
                                                | Entrega: {{ \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('pedidos.show', $pedido->id) }}" 
                                           class="text-gray-600 hover:text-gray-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($pedidos->hasPages())
                            <div class="mt-6">
                                {{ $pedidos->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay pedidos</h3>
                            <p class="mt-1 text-sm text-gray-500">Este cliente aún no tiene pedidos registrados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
