@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crear Producto</h1>
                <p class="mt-2 text-sm text-gray-600">Agrega un nuevo producto al catálogo</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('productos.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Información Básica -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Producto</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Producto *</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="categoria" class="block text-sm font-medium text-gray-700">Categoría *</label>
                    <select name="categoria" id="categoria" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                        <option value="">Seleccionar categoría</option>
                        <option value="servicio" {{ old('categoria') == 'servicio' ? 'selected' : '' }}>Servicio</option>
                        <option value="impresion" {{ old('categoria') == 'impresion' ? 'selected' : '' }}>Impresión</option>
                        <option value="fotocopia" {{ old('categoria') == 'fotocopia' ? 'selected' : '' }}>Fotocopia</option>
                    </select>
                    @error('categoria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="precio_venta" class="block text-sm font-medium text-gray-700">Precio de Venta *</label>
                    <input type="number" name="precio_venta" id="precio_venta" step="0.01" min="0" value="{{ old('precio_venta') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                    @error('precio_venta')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        <span class="ml-2 text-sm text-gray-700">Producto activo</span>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Configuración de Impresión/Fotocopia -->
        <div id="configuracion-impresion" class="bg-white shadow-sm rounded-lg p-6" style="display: none;">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Impresión/Fotocopia</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="maquina_id" class="block text-sm font-medium text-gray-700">Máquina *</label>
                    <select name="maquina_id" id="maquina_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                        <option value="">Seleccionar máquina</option>
                        @foreach($maquinas as $maquina)
                            <option value="{{ $maquina->id }}" {{ old('maquina_id') == $maquina->id ? 'selected' : '' }}>
                                {{ $maquina->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('maquina_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo_impresion" class="block text-sm font-medium text-gray-700">Tipo de Impresión *</label>
                    <select name="tipo_impresion" id="tipo_impresion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                        <option value="">Seleccionar tipo</option>
                        <option value="b&n" {{ old('tipo_impresion') == 'b&n' ? 'selected' : '' }}>Blanco y Negro</option>
                        <option value="color" {{ old('tipo_impresion') == 'color' ? 'selected' : '' }}>Color</option>
                    </select>
                    @error('tipo_impresion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="insumo_id" class="block text-sm font-medium text-gray-700">Insumo (Papel) *</label>
                    <select name="insumo_id" id="insumo_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                        <option value="">Seleccionar insumo</option>
                        @foreach($insumos as $insumo)
                            <option value="{{ $insumo->id }}" {{ old('insumo_id') == $insumo->id ? 'selected' : '' }}>
                                {{ $insumo->nombre }} - ${{ number_format($insumo->costo_por_unidad, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('insumo_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Error Message -->
        @if ($errors->has('error'))
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $errors->first('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Botones -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800">
                Crear Producto
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoria');
    const configuracionImpresion = document.getElementById('configuracion-impresion');
    const maquinaSelect = document.getElementById('maquina_id');
    const tipoImpresionSelect = document.getElementById('tipo_impresion');
    const insumoSelect = document.getElementById('insumo_id');

    function toggleConfiguracionImpresion() {
        const categoria = categoriaSelect.value;
        if (categoria === 'impresion' || categoria === 'fotocopia') {
            configuracionImpresion.style.display = 'block';
            maquinaSelect.required = true;
            tipoImpresionSelect.required = true;
            insumoSelect.required = true;
        } else {
            configuracionImpresion.style.display = 'none';
            maquinaSelect.required = false;
            tipoImpresionSelect.required = false;
            insumoSelect.required = false;
            maquinaSelect.value = '';
            tipoImpresionSelect.value = '';
            insumoSelect.value = '';
        }
    }

    categoriaSelect.addEventListener('change', toggleConfiguracionImpresion);
    
    // Ejecutar al cargar la página
    toggleConfiguracionImpresion();
});
</script>
@endsection
