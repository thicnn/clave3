@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crear Pedido</h1>
                <p class="mt-2 text-sm text-gray-600">Crea un nuevo pedido seleccionando productos del catálogo</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('pedidos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('pedidos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="pedidoForm">
        @csrf

        <!-- Información Básica -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Pedido</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
                    <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente *</label>
                    <div class="relative">
                        <input type="text" id="cliente_search" placeholder="Buscar por nombre, email o teléfono..." 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                        <input type="hidden" name="cliente_id" id="cliente_id" value="{{ old('cliente_id') }}">
                        <div id="cliente_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <div id="cliente_selected" class="mt-2 p-2 bg-gray-50 rounded-md hidden">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-900"></span>
                            <button type="button" id="cliente_clear" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
            @error('cliente_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
                    <label for="fecha_entrega" class="block text-sm font-medium text-gray-700">Fecha de Entrega</label>
                    <input type="date" name="fecha_entrega" id="fecha_entrega" value="{{ old('fecha_entrega') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
            @error('fecha_entrega')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado *</label>
                    <select name="estado" id="estado" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                        <option value="Solicitud" {{ old('estado', 'Solicitud') == 'Solicitud' ? 'selected' : '' }}>Solicitud</option>
                        <option value="Cotización" {{ old('estado') == 'Cotización' ? 'selected' : '' }}>Cotización</option>
                        <option value="Confirmado" {{ old('estado') == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="En Curso" {{ old('estado') == 'En Curso' ? 'selected' : '' }}>En Curso</option>
                        <option value="Listo para Retirar" {{ old('estado') == 'Listo para Retirar' ? 'selected' : '' }}>Listo para Retirar</option>
                        <option value="Entregado" {{ old('estado') == 'Entregado' ? 'selected' : '' }}>Entregado</option>
            </select>
            @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700">Notas</label>
                    <textarea name="notas" id="notas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">{{ old('notas') }}</textarea>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Productos</h3>
            
            <div id="productos-container">
                <!-- Los productos se agregarán dinámicamente aquí -->
            </div>

            <div class="mt-4">
                <button type="button" id="agregar-producto" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Agregar Producto
                </button>
            </div>
        </div>

        <!-- Archivos -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Archivos</h3>

        <div>
                <label for="archivos" class="block text-sm font-medium text-gray-700">Subir archivos</label>
                <input type="file" name="archivos[]" id="archivos" multiple accept=".pdf,.jpg,.png,.zip,.rar" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                <p class="mt-1 text-sm text-gray-500">Formatos permitidos: PDF, JPG, PNG, ZIP, RAR (máx. 100MB cada uno)</p>
            @error('archivos.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('pedidos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancelar
            </a>
            <button type="button" id="test-button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Test (Ver Logs)
            </button>
            <button type="button" id="test-maquina-button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                Test Máquina
            </button>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800">
                Crear Pedido
            </button>
        </div>
    </form>
</div>

<!-- Template para productos -->
<template id="producto-template">
    <div class="producto-item border border-gray-200 rounded-lg p-4 mb-4">
        <div class="flex justify-between items-start mb-4">
            <h4 class="text-md font-medium text-gray-900">Producto <span class="producto-numero"></span></h4>
            <button type="button" class="eliminar-producto text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría *</label>
                        <select name="items[INDEX][categoria]" class="categoria-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="servicio">Servicio</option>
                            <option value="impresion">Impresión</option>
                            <option value="fotocopia">Fotocopia</option>
                        </select>
                    </div>

                    <div class="maquina-container">
                        <label class="block text-sm font-medium text-gray-700">Máquina</label>
                        <select name="items[INDEX][maquina_id]" class="maquina-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" disabled>
                            <option value="">Seleccionar máquina</option>
                            @foreach($maquinas as $maquina)
                                <option value="{{ $maquina->id }}" 
                                        data-color="{{ $maquina->costo_color_carilla ? 'true' : 'false' }}">
                                    {{ $maquina->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                    <div class="tipo-container">
                        <label class="block text-sm font-medium text-gray-700">Tipo de Impresión</label>
                        <select name="items[INDEX][tipo_impresion]" class="tipo-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" disabled>
                            <option value="">Seleccionar tipo</option>
                            <option value="b&n">Blanco y Negro</option>
                            <option value="color">Color</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Producto *</label>
                        <select name="items[INDEX][producto_id]" class="producto-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" disabled required>
                            <option value="">Seleccionar producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" 
                                        data-categoria="{{ $producto->categoria }}"
                                        data-maquina="{{ $producto->maquina ? $producto->maquina->id : '' }}"
                                        data-tipo="{{ $producto->tipo_impresion }}"
                                        data-insumo="{{ $producto->insumo ? $producto->insumo->nombre : '' }}"
                                        data-precio="{{ $producto->precio_venta }}">
                                    {{ $producto->nombre }} - {{ $producto->insumo ? $producto->insumo->nombre : 'Sin insumo' }}
                                    - ${{ number_format($producto->precio_venta, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad *</label>
                        <input type="number" name="items[INDEX][cantidad]" min="1" value="1" class="cantidad-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descuento (%)</label>
                        <input type="number" name="items[INDEX][descuento_item]" min="0" max="100" value="0" class="descuento-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                    </div>

                    <div class="doble-faz-container">
                        <label class="flex items-center mt-6">
                            <input type="checkbox" name="items[INDEX][es_doble_faz]" value="1" class="doble-faz-checkbox rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="ml-2 text-sm text-gray-700">Doble faz</span>
                        </label>
                    </div>
                </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="lg:col-span-2">
                <div class="producto-info text-sm text-gray-600">
                    <!-- Información del producto se mostrará aquí -->
                </div>
            </div>
        </div>

        <div class="mt-4 p-3 bg-gray-50 rounded-md">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700">Total del ítem:</span>
                <span class="text-lg font-semibold text-gray-900 item-total">$0.00</span>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productoIndex = 0;
    const productosContainer = document.getElementById('productos-container');
    const agregarProductoBtn = document.getElementById('agregar-producto');
    const productoTemplate = document.getElementById('producto-template');
    
    // Verificar que los elementos existen
    if (!agregarProductoBtn) {
        console.error('Botón agregar producto no encontrado');
        return;
    }
    if (!productosContainer) {
        console.error('Contenedor de productos no encontrado');
        return;
    }
    if (!productoTemplate) {
        console.error('Template de producto no encontrado');
        return;
    }

    // Búsqueda de clientes
    const clienteSearch = document.getElementById('cliente_search');
    const clienteId = document.getElementById('cliente_id');
    const clienteResults = document.getElementById('cliente_results');
    const clienteSelected = document.getElementById('cliente_selected');
    const clienteClear = document.getElementById('cliente_clear');
    let searchTimeout;

    clienteSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            clienteResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            buscarClientes(query);
        }, 300);
    });

    function buscarClientes(query) {
        fetch(`/api/clientes/buscar?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                mostrarResultadosClientes(data);
            })
            .catch(error => {
                console.error('Error al buscar clientes:', error);
            });
    }

    function mostrarResultadosClientes(clientes) {
        clienteResults.innerHTML = '';
        
        if (clientes.length === 0) {
            clienteResults.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron clientes</div>';
        } else {
            clientes.forEach(cliente => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                div.innerHTML = `
                    <div class="font-medium text-gray-900">${cliente.nombre}</div>
                    <div class="text-sm text-gray-500">
                        ${cliente.email ? `Email: ${cliente.email}` : ''}
                        ${cliente.telefono ? `Tel: ${cliente.telefono}` : ''}
                        ${cliente.rut ? `RUT: ${cliente.rut}` : ''}
                    </div>
                `;
                div.addEventListener('click', () => seleccionarCliente(cliente));
                clienteResults.appendChild(div);
            });
        }
        
        clienteResults.classList.remove('hidden');
    }

    function seleccionarCliente(cliente) {
        clienteId.value = cliente.id;
        clienteSearch.value = '';
        clienteResults.classList.add('hidden');
        
        const clienteInfo = `${cliente.nombre}${cliente.email ? ` - ${cliente.email}` : ''}${cliente.telefono ? ` - ${cliente.telefono}` : ''}`;
        clienteSelected.querySelector('span').textContent = clienteInfo;
        clienteSelected.classList.remove('hidden');
    }

    clienteClear.addEventListener('click', function() {
        clienteId.value = '';
        clienteSelected.classList.add('hidden');
    });

    // Ocultar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!clienteSearch.contains(e.target) && !clienteResults.contains(e.target)) {
            clienteResults.classList.add('hidden');
        }
    });

    // Función para agregar producto
    function agregarProducto() {
        console.log('=== AGREGANDO PRODUCTO ===');
        
        // Crear HTML directamente
        const index = productoIndex++;
        const html = `
            <div class="producto-item border border-gray-200 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start mb-4">
                    <h4 class="text-md font-medium text-gray-900">Producto ${index + 1}</h4>
                    <button type="button" class="eliminar-producto text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría *</label>
                        <select name="items[${index}][categoria]" class="categoria-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="servicio">Servicio</option>
                            <option value="impresion">Impresión</option>
                            <option value="fotocopia">Fotocopia</option>
                        </select>
                    </div>

                    <div class="maquina-container">
                        <label class="block text-sm font-medium text-gray-700">Máquina</label>
                        <select name="items[${index}][maquina_id]" class="maquina-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" disabled>
                            <option value="">Seleccionar máquina</option>
                            @foreach($maquinas as $maquina)
                                <option value="{{ $maquina->id }}" 
                                        data-color="{{ $maquina->costo_color_carilla ? 'true' : 'false' }}">
                                    {{ $maquina->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                    <div class="tipo-container">
                        <label class="block text-sm font-medium text-gray-700">Tipo de Impresión</label>
                        <select name="items[${index}][tipo_impresion]" class="tipo-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" disabled>
                            <option value="">Seleccionar tipo</option>
                            <option value="b&n">Blanco y Negro</option>
                            <option value="color">Color</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Producto *</label>
                        <select name="items[${index}][producto_id]" class="producto-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" disabled required>
                            <option value="">Seleccionar producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" 
                                        data-categoria="{{ $producto->categoria }}"
                                        data-maquina="{{ $producto->maquina ? $producto->maquina->id : '' }}"
                                        data-tipo="{{ $producto->tipo_impresion }}"
                                        data-insumo="{{ $producto->insumo ? $producto->insumo->nombre : '' }}"
                                        data-precio="{{ $producto->precio_venta }}">
                                    {{ $producto->nombre }} - {{ $producto->insumo ? $producto->insumo->nombre : 'Sin insumo' }}
                                    - ${{ number_format($producto->precio_venta, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad *</label>
                        <input type="number" name="items[${index}][cantidad]" min="1" value="1" class="cantidad-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descuento (%)</label>
                        <input type="number" name="items[${index}][descuento_item]" min="0" max="100" value="0" class="descuento-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                    </div>

                    <div class="doble-faz-container">
                        <label class="flex items-center mt-6">
                            <input type="checkbox" name="items[${index}][es_doble_faz]" value="1" class="doble-faz-checkbox rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="ml-2 text-sm text-gray-700">Doble faz</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                    <div class="lg:col-span-2">
                        <div class="producto-info text-sm text-gray-600">
                            <!-- Información del producto se mostrará aquí -->
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-gray-50 rounded-md">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total del ítem:</span>
                        <span class="text-lg font-semibold text-gray-900 item-total">$0.00</span>
                    </div>
                </div>
            </div>
        `;
        
        // Crear elemento
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const finalElement = tempDiv.firstElementChild;
        
        // Agregar event listeners
        const categoriaSelect = finalElement.querySelector('.categoria-select');
        const maquinaSelect = finalElement.querySelector('.maquina-select');
        const tipoSelect = finalElement.querySelector('.tipo-select');
        const productoSelect = finalElement.querySelector('.producto-select');
        const cantidadInput = finalElement.querySelector('.cantidad-input');
        const descuentoInput = finalElement.querySelector('.descuento-input');
        const dobleFazCheckbox = finalElement.querySelector('.doble-faz-checkbox');
        const eliminarBtn = finalElement.querySelector('.eliminar-producto');
        
        // Event listeners
        if (categoriaSelect) categoriaSelect.addEventListener('change', () => actualizarCascada(finalElement));
        if (maquinaSelect) maquinaSelect.addEventListener('change', () => manejarSeleccionMaquina(finalElement));
        if (tipoSelect) tipoSelect.addEventListener('change', () => manejarSeleccionTipo(finalElement));
        if (productoSelect) productoSelect.addEventListener('change', () => actualizarProductoInfo(finalElement));
        if (cantidadInput) cantidadInput.addEventListener('input', () => calcularTotal(finalElement));
        if (descuentoInput) descuentoInput.addEventListener('input', () => calcularTotal(finalElement));
        if (dobleFazCheckbox) dobleFazCheckbox.addEventListener('change', () => calcularTotal(finalElement));
        if (eliminarBtn) eliminarBtn.addEventListener('click', () => {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                eliminarProducto(finalElement);
            }
        });
        
        // Agregar al contenedor
        productosContainer.appendChild(finalElement);
        console.log('Producto agregado exitosamente');
    }

    // Event listener para el botón
    agregarProductoBtn.addEventListener('click', agregarProducto);

    // Botón de prueba
    const testButton = document.getElementById('test-button');
    if (testButton) {
        testButton.addEventListener('click', function() {
            console.log('=== BOTÓN TEST PRESIONADO ===');
            
            // Verificar que hay al menos un producto
            const productos = document.querySelectorAll('.producto-item');
            console.log('Productos encontrados:', productos.length);
            
            if (productos.length === 0) {
                alert('Debe agregar al menos un producto');
                return;
            }
            
            // Verificar que todos los productos tienen producto_id seleccionado
            let productosValidos = 0;
            productos.forEach((producto, index) => {
                const productoSelect = producto.querySelector('.producto-select');
                const categoriaSelect = producto.querySelector('.categoria-select');
                const maquinaSelect = producto.querySelector('.maquina-select');
                const tipoSelect = producto.querySelector('.tipo-select');
                const cantidadInput = producto.querySelector('.cantidad-input');
                
                console.log(`--- Producto ${index + 1} ---`);
                console.log('Categoría:', categoriaSelect ? categoriaSelect.value : 'N/A');
                console.log('Máquina:', maquinaSelect ? maquinaSelect.value : 'N/A');
                console.log('Tipo:', tipoSelect ? tipoSelect.value : 'N/A');
                console.log('Producto ID:', productoSelect ? productoSelect.value : 'N/A');
                console.log('Cantidad:', cantidadInput ? cantidadInput.value : 'N/A');
                
                if (productoSelect && productoSelect.value) {
                    productosValidos++;
                    console.log('✅ Producto válido');
                } else {
                    console.log('❌ Producto inválido');
                }
            });
            
            console.log(`Total de productos válidos: ${productosValidos}`);
            
            if (productosValidos === 0) {
                alert('Debe seleccionar al menos un producto válido');
                return;
            }
            
            console.log('=== FORMULARIO VÁLIDO PARA ENVÍO ===');
            alert('Formulario válido! Revisa la consola para ver los detalles.');
        });
    }

    // Botón de prueba de máquina
    const testMaquinaButton = document.getElementById('test-maquina-button');
    if (testMaquinaButton) {
        testMaquinaButton.addEventListener('click', function() {
            console.log('=== BOTÓN TEST MÁQUINA PRESIONADO ===');
            
            // Agregar un producto si no hay ninguno
            if (document.querySelectorAll('.producto-item').length === 0) {
                agregarProducto();
            }
            
            // Seleccionar categoría impresión
            const categoriaSelect = document.querySelector('.categoria-select');
            if (categoriaSelect) {
                categoriaSelect.value = 'impresion';
                categoriaSelect.dispatchEvent(new Event('change'));
                console.log('Categoría seleccionada: impresión');
            }
            
            // Cargar máquinas
            const productoElement = document.querySelector('.producto-item');
            if (productoElement) {
                cargarMaquinas(productoElement);
                console.log('Máquinas cargadas');
            }
        });
    }

    // Debuggear el envío del formulario
    const pedidoForm = document.getElementById('pedidoForm');
    if (pedidoForm) {
        pedidoForm.addEventListener('submit', function(e) {
            console.log('=== FORMULARIO ENVIADO ===');
            
            // Verificar que hay al menos un producto
            const productos = document.querySelectorAll('.producto-item');
            console.log('Productos encontrados:', productos.length);
            
            if (productos.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto');
                return false;
            }
            
            // Verificar que todos los productos tienen producto_id seleccionado
            let productosValidos = 0;
            productos.forEach((producto, index) => {
                const productoSelect = producto.querySelector('.producto-select');
                const categoriaSelect = producto.querySelector('.categoria-select');
                const maquinaSelect = producto.querySelector('.maquina-select');
                const tipoSelect = producto.querySelector('.tipo-select');
                const cantidadInput = producto.querySelector('.cantidad-input');
                
                console.log(`--- Producto ${index + 1} ---`);
                console.log('Categoría:', categoriaSelect ? categoriaSelect.value : 'N/A');
                console.log('Máquina:', maquinaSelect ? maquinaSelect.value : 'N/A');
                console.log('Tipo:', tipoSelect ? tipoSelect.value : 'N/A');
                console.log('Producto ID:', productoSelect ? productoSelect.value : 'N/A');
                console.log('Cantidad:', cantidadInput ? cantidadInput.value : 'N/A');
                
                if (productoSelect && productoSelect.value) {
                    productosValidos++;
                    console.log('✅ Producto válido');
                } else {
                    console.log('❌ Producto inválido');
                }
            });
            
            console.log(`Total de productos válidos: ${productosValidos}`);
            
            if (productosValidos === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un producto válido');
                return false;
            }
            
            console.log('=== FORMULARIO VÁLIDO, ENVIANDO ===');
            
            // Mostrar todos los datos que se van a enviar
            const formData = new FormData(pedidoForm);
            console.log('=== DATOS DEL FORMULARIO ===');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            // NO PREVENIR EL ENVÍO - DEJAR QUE SE ENVÍE
            // e.preventDefault(); // COMENTADO PARA QUE SE ENVÍE
        });
    }

    // Función para actualizar la cascada de selección
    function actualizarCascada(productoElement) {
        console.log('=== ACTUALIZANDO CASCADA ===');
        
        const categoriaSelect = productoElement.querySelector('.categoria-select');
        const maquinaSelect = productoElement.querySelector('.maquina-select');
        const tipoSelect = productoElement.querySelector('.tipo-select');
        const productoSelect = productoElement.querySelector('.producto-select');
        const dobleFazCheckbox = productoElement.querySelector('.doble-faz-checkbox');
        
        const categoria = categoriaSelect.value;
        console.log('Categoría seleccionada:', categoria);
        
        // Resetear selects dependientes
        maquinaSelect.disabled = true;
        tipoSelect.disabled = true;
        productoSelect.disabled = true;
        
        // Limpiar opciones
        maquinaSelect.innerHTML = '<option value="">Seleccionar máquina</option>';
        tipoSelect.innerHTML = '<option value="">Seleccionar tipo</option>';
        productoSelect.innerHTML = '<option value="">Seleccionar producto</option>';
        
        // Resetear doble faz
        if (dobleFazCheckbox) {
            dobleFazCheckbox.checked = false;
        }
        
        if (categoria === 'servicio') {
            console.log('Categoría: Servicio - Ocultando máquina, tipo, doble faz');
            // Para servicios, no necesita máquina ni tipo, y no puede ser doble faz
            const maquinaContainer = productoElement.querySelector('.maquina-container');
            const tipoContainer = productoElement.querySelector('.tipo-container');
            const dobleFazContainer = productoElement.querySelector('.doble-faz-container');
            
            if (maquinaContainer) maquinaContainer.style.display = 'none';
            if (tipoContainer) tipoContainer.style.display = 'none';
            if (dobleFazContainer) dobleFazContainer.style.display = 'none';
            
            productoSelect.disabled = false;
            filtrarProductos(productoElement, categoria, '', '');
        } else if (categoria === 'impresion' || categoria === 'fotocopia') {
            console.log('Categoría: Impresión/Fotocopia - Mostrando máquina, tipo, doble faz');
            // Para impresión/fotocopia, mostrar máquina y doble faz
            const maquinaContainer = productoElement.querySelector('.maquina-container');
            const tipoContainer = productoElement.querySelector('.tipo-container');
            const dobleFazContainer = productoElement.querySelector('.doble-faz-container');
            
            if (maquinaContainer) maquinaContainer.style.display = 'block';
            if (tipoContainer) tipoContainer.style.display = 'block';
            if (dobleFazContainer) dobleFazContainer.style.display = 'block';
            
            // Habilitar máquina y cargar opciones
            maquinaSelect.disabled = false;
            cargarMaquinas(productoElement);
        } else {
            console.log('Categoría: No seleccionada - Mostrando todo');
            // Categoría no seleccionada
            const maquinaContainer = productoElement.querySelector('.maquina-container');
            const tipoContainer = productoElement.querySelector('.tipo-container');
            const dobleFazContainer = productoElement.querySelector('.doble-faz-container');
            
            if (maquinaContainer) maquinaContainer.style.display = 'block';
            if (tipoContainer) tipoContainer.style.display = 'block';
            if (dobleFazContainer) dobleFazContainer.style.display = 'block';
        }
    }

    // Función para cargar máquinas
    function cargarMaquinas(productoElement) {
        console.log('=== CARGANDO MÁQUINAS ===');
        
        const maquinaSelect = productoElement.querySelector('.maquina-select');
        const categoriaSelect = productoElement.querySelector('.categoria-select');
        
        // Obtener todas las máquinas disponibles
        const maquinas = @json($maquinas);
        console.log('Máquinas disponibles:', maquinas);
        
        // Limpiar opciones existentes
        maquinaSelect.innerHTML = '<option value="">Seleccionar máquina</option>';
        
        maquinas.forEach(maquina => {
            const option = document.createElement('option');
            option.value = maquina.id;
            option.textContent = maquina.nombre;
            option.dataset.color = maquina.costo_color_carilla ? 'true' : 'false';
            maquinaSelect.appendChild(option);
        });
        
        console.log('Máquinas cargadas en el select');
    }
    
    // Función para manejar selección de máquina
    function manejarSeleccionMaquina(productoElement) {
        const maquinaSelect = productoElement.querySelector('.maquina-select');
        const tipoSelect = productoElement.querySelector('.tipo-select');
        const productoSelect = productoElement.querySelector('.producto-select');
        const categoriaSelect = productoElement.querySelector('.categoria-select');
        
        console.log('=== MÁQUINA SELECCIONADA ===');
        console.log('Máquina ID:', maquinaSelect.value);
        
        tipoSelect.disabled = false;
        productoSelect.disabled = true;
        
        // Limpiar tipo y producto
        tipoSelect.innerHTML = '<option value="">Seleccionar tipo</option>';
        productoSelect.innerHTML = '<option value="">Seleccionar producto</option>';
        
        // Agregar opciones de tipo
        tipoSelect.innerHTML += '<option value="b&n">Blanco y Negro</option>';
        
        const maquinaSeleccionada = maquinaSelect.selectedOptions[0];
        console.log('Máquina seleccionada:', maquinaSeleccionada.textContent);
        console.log('Soporta color:', maquinaSeleccionada.dataset.color);
        
        if (maquinaSeleccionada && maquinaSeleccionada.dataset.color === 'true') {
            tipoSelect.innerHTML += '<option value="color">Color</option>';
        }
        
        // Si es BH227, seleccionar automáticamente B&N
        if (maquinaSeleccionada && maquinaSeleccionada.textContent.toLowerCase().includes('bh227')) {
            console.log('=== BH227 DETECTADA ===');
            console.log('Asignando B&N automáticamente');
            
            // Asignar inmediatamente
            tipoSelect.value = 'b&n';
            console.log('Tipo asignado:', tipoSelect.value);
            
            // Disparar el evento change para cargar productos
            setTimeout(() => {
                console.log('Disparando evento change para cargar productos');
                const changeEvent = new Event('change', { bubbles: true });
                tipoSelect.dispatchEvent(changeEvent);
            }, 100);
        }
    }
    
    // Función para manejar selección de tipo
    function manejarSeleccionTipo(productoElement) {
        const tipoSelect = productoElement.querySelector('.tipo-select');
        const productoSelect = productoElement.querySelector('.producto-select');
        const maquinaSelect = productoElement.querySelector('.maquina-select');
        const categoriaSelect = productoElement.querySelector('.categoria-select');
        
        console.log('=== TIPO SELECCIONADO ===');
        console.log('Tipo:', tipoSelect.value);
        
        productoSelect.disabled = false;
        filtrarProductos(productoElement, categoriaSelect.value, maquinaSelect.value, tipoSelect.value);
    }

    // Función para filtrar productos según criterios
    function filtrarProductos(productoElement, categoria, maquina, tipo) {
        const productoSelect = productoElement.querySelector('.producto-select');
        const todosProductos = @json($productos);
        
        // Limpiar opciones
        productoSelect.innerHTML = '<option value="">Seleccionar producto</option>';
        
        // Filtrar productos
        const productosFiltrados = todosProductos.filter(producto => {
            if (categoria === 'servicio') {
                return producto.categoria === 'servicio';
            } else {
                return producto.categoria === categoria && 
                       producto.maquina_id == maquina && 
                       producto.tipo_impresion === tipo;
            }
        });
        
        // Agregar opciones filtradas
        productosFiltrados.forEach(producto => {
            const option = document.createElement('option');
            option.value = producto.id;
            option.textContent = `${producto.nombre} - ${producto.insumo ? producto.insumo.nombre : 'Sin insumo'} - $${parseFloat(producto.precio_venta).toFixed(2)}`;
            option.dataset.categoria = producto.categoria;
            option.dataset.maquina = producto.maquina_id || '';
            option.dataset.tipo = producto.tipo_impresion || '';
            option.dataset.insumo = producto.insumo ? producto.insumo.nombre : '';
            option.dataset.precio = producto.precio_venta;
            productoSelect.appendChild(option);
        });
    }

    function actualizarProductoInfo(productoElement) {
        const select = productoElement.querySelector('.producto-select');
        const infoDiv = productoElement.querySelector('.producto-info');
        const option = select.selectedOptions[0];
        
        if (option && option.value) {
            const categoria = option.dataset.categoria;
            const maquina = option.dataset.maquina;
            const tipo = option.dataset.tipo;
            const insumo = option.dataset.insumo;
            
            let info = `<strong>Categoría:</strong> ${categoria.charAt(0).toUpperCase() + categoria.slice(1)}`;
            
            if (maquina) {
                info += `<br><strong>Máquina:</strong> ${maquina}`;
            }
            if (tipo) {
                info += `<br><strong>Tipo:</strong> ${tipo.toUpperCase()}`;
            }
            if (insumo) {
                info += `<br><strong>Insumo:</strong> ${insumo}`;
            }
            
            infoDiv.innerHTML = info;
        } else {
            infoDiv.innerHTML = '';
        }
        
        calcularTotal(productoElement);
    }

    function calcularTotal(productoElement) {
        const select = productoElement.querySelector('.producto-select');
        const cantidadInput = productoElement.querySelector('.cantidad-input');
        const descuentoInput = productoElement.querySelector('.descuento-input');
        const dobleFazCheckbox = productoElement.querySelector('.doble-faz-checkbox');
        const totalSpan = productoElement.querySelector('.item-total');
        
        const option = select.selectedOptions[0];
        if (!option || !option.value) {
            totalSpan.textContent = '$0.00';
            return;
        }
        
        const precioBase = parseFloat(option.dataset.precio) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        const descuento = parseFloat(descuentoInput.value) || 0;
        const esDobleFaz = dobleFazCheckbox.checked;
        
        // Para doble faz, el precio se mantiene igual (no se reduce a la mitad)
        // Solo se usa menos papel físico, pero el precio es por copia
        const subtotal = precioBase * cantidad;
        const montoDescuento = subtotal * (descuento / 100);
        const total = subtotal - montoDescuento;
        
        totalSpan.textContent = `$${total.toFixed(2)}`;
    }

    function eliminarProducto(productoElement) {
        productoElement.remove();
    }
});
</script>
@endsection