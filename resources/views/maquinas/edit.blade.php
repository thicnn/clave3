@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Editar Máquina</h1>
        <p class="mt-2 text-sm text-gray-600">Modifica los datos de la máquina</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('maquinas.update', $maquina->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $maquina->nombre) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">{{ old('descripcion', $maquina->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="costo_bn_carilla" class="block text-sm font-medium text-gray-700 mb-1">Costo B&N por Carilla *</label>
                    <input type="number" id="costo_bn_carilla" name="costo_bn_carilla" step="0.01" min="0" value="{{ old('costo_bn_carilla', $maquina->costo_bn_carilla) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                    @error('costo_bn_carilla')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="costo_color_carilla" class="block text-sm font-medium text-gray-700 mb-1">Costo Color por Carilla</label>
                    <input type="number" id="costo_color_carilla" name="costo_color_carilla" step="0.01" min="0" value="{{ old('costo_color_carilla', $maquina->costo_color_carilla) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                    @error('costo_color_carilla')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" id="activa" name="activa" value="1" {{ old('activa', $maquina->activa) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-gray-600 shadow-sm focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Máquina activa</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('maquinas.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800">
                    Actualizar Máquina
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
