@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600">Resumen del sistema de gestión</p>
    </div>

    <!-- Alertas de Stock -->
    @if($alertasStock->isNotEmpty())
        <div class="mb-8 bg-red-50 border border-red-200 rounded-lg">
            <div class="px-6 py-4 border-b border-red-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-red-800">Alertas de Stock Bajo</h3>
                </div>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-3">
                    @foreach ($alertasStock as $insumo)
                        <div class="flex items-center justify-between p-3 bg-red-100 rounded-lg">
                            <div>
                                <a href="{{ route('insumos.edit', $insumo->id) }}" class="text-sm font-medium text-red-900 hover:text-red-700">
                                    {{ $insumo->nombre }}
                                </a>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-800">
                                Stock: {{ $insumo->stock_actual }} (Mín: {{ $insumo->stock_minimo }})
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="mb-8 bg-green-50 border border-green-200 rounded-lg">
            <div class="px-6 py-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800">¡No hay problemas de stock!</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Estadísticas del Día -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pedidos Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pedidosHoy }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Ventas Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($ventasHoy, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Meta Mensual</p>
                    <div class="flex items-center">
                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ min(100, $progresoVentas) }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($progresoVentas, 1) }}%</span>
                    </div>
                    <p class="text-xs text-gray-500">${{ number_format($ventasMesActual, 0) }} / ${{ number_format($metaVentasMensual, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pedidos para Entregar -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Pedidos para Entregar</h3>
                </div>
            </div>
            <div class="px-6 py-4">
                @if($pedidosParaEntregar->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($pedidosParaEntregar as $pedido)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">#{{ $pedido->id }} - {{ $pedido->cliente->nombre }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') : 'Sin fecha' }}
                                    </div>
                                </div>
                                <a href="{{ route('pedidos.show', $pedido->id) }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Ver
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No hay pedidos listos para entregar.</p>
                @endif
            </div>
        </div>

        <!-- Pedidos Recientes -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Pedidos Recientes</h3>
                </div>
            </div>
            <div class="px-6 py-4">
                @if($pedidosRecientes->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($pedidosRecientes as $pedido)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">#{{ $pedido->id }} - {{ $pedido->cliente->nombre }}</div>
                                    <div class="flex items-center mt-1">
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
                                <a href="{{ route('pedidos.show', $pedido->id) }}" 
                                   class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                                    Ver
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No se han registrado pedidos recientemente.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Estadísticas Adicionales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Top Clientes -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Clientes</h3>
            </div>
            <div class="px-6 py-4">
                @if($topClientes->count() > 0)
                    <div class="space-y-3">
                        @foreach($topClientes as $cliente)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-900">{{ $cliente->nombre }}</span>
                                <span class="text-sm font-medium text-gray-500">${{ number_format($cliente->total_ventas, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No hay datos de clientes disponibles.</p>
                @endif
            </div>
        </div>

        <!-- Insumos Más Usados -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Insumos Más Usados</h3>
            </div>
            <div class="px-6 py-4">
                @if($insumosMasUsados->count() > 0)
                    <div class="space-y-3">
                        @foreach($insumosMasUsados as $insumo)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-900">{{ $insumo->nombre }}</span>
                                <span class="text-sm font-medium text-gray-500">{{ $insumo->total_usado }} unidades</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No hay datos de insumos disponibles.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Acciones Rápidas</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('pedidos.create') }}" 
                   class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Nuevo Pedido</div>
                        <div class="text-xs text-gray-500">Crear pedido</div>
                    </div>
                </a>
                
                <a href="{{ route('clientes.create') }}" 
                   class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Nuevo Cliente</div>
                        <div class="text-xs text-gray-500">Registrar cliente</div>
                    </div>
                </a>
                
                <a href="{{ route('insumos.create') }}" 
                   class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Nuevo Insumo</div>
                        <div class="text-xs text-gray-500">Agregar insumo</div>
                    </div>
                </a>
                
                <a href="{{ route('pedidos.index') }}" 
                   class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Ver Pedidos</div>
                        <div class="text-xs text-gray-500">Lista completa</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection