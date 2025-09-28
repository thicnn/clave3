@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $producto->nombre }}</h1>
                <p class="mt-2 text-sm text-gray-600">Detalles del producto</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('productos.edit', $producto->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Información General</h3>
        </div>
        
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $producto->nombre }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                    <dd class="mt-1">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $producto->categoria === 'servicio' ? 'bg-blue-100 text-blue-800' : 
                               ($producto->categoria === 'impresion' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ ucfirst($producto->categoria) }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Precio de Venta</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($producto->precio_venta, 2) }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                        @if($producto->activo)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Activo
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Inactivo
                            </span>
                        @endif
                    </dd>
                </div>

                @if($producto->descripcion)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $producto->descripcion }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Configuración de Impresión/Fotocopia -->
    @if(in_array($producto->categoria, ['impresion', 'fotocopia']))
        <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Configuración de Impresión/Fotocopia</h3>
            </div>
            
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Máquina</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $producto->maquina ? $producto->maquina->nombre : 'No asignada' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo de Impresión</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $producto->tipo_impresion ? ucfirst($producto->tipo_impresion) : 'No especificado' }}</dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Insumo (Papel)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $producto->insumo ? $producto->insumo->nombre : 'No asignado' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif

    <!-- Información Financiera (Solo para Administradores) -->
    @if(auth()->user()->isAdmin())
        <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Información Financiera</h3>
            </div>
            
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Costo de Producción</dt>
                        <dd class="mt-1 text-sm text-gray-900">${{ number_format($producto->costo_produccion, 2) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ganancia</dt>
                        <dd class="mt-1 text-sm text-green-600 font-semibold">${{ number_format($producto->calcularGanancia(), 2) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Margen de Ganancia</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($producto->calcularMargenGanancia(), 1) }}%</dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif

    <!-- Acciones -->
    <div class="mt-6 flex justify-end space-x-4">
        <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50"
                    onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Eliminar
            </button>
        </form>
    </div>
</div>
@endsection
