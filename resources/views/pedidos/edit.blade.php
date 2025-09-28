@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Pedido #{{ $pedido->id }}</h1>
                <p class="mt-2 text-sm text-gray-600">Modifica la información del pedido</p>
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
    <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Información Básica -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Pedido</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md border">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $pedido->cliente ? $pedido->cliente->nombre : 'Sin cliente' }}</span>
                                @if($pedido->cliente)
                                    <div class="text-xs text-gray-500">
                                        {{ $pedido->cliente->email ? 'Email: ' . $pedido->cliente->email : '' }}
                                        {{ $pedido->cliente->telefono ? 'Tel: ' . $pedido->cliente->telefono : '' }}
                                    </div>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">No se puede modificar</span>
                        </div>
                    </div>
                    <input type="hidden" name="cliente_id" value="{{ $pedido->cliente_id }}">
                </div>

                <div>
                    <label for="fecha_entrega" class="block text-sm font-medium text-gray-700">Fecha de Entrega</label>
                    <input type="date" name="fecha_entrega" id="fecha_entrega" value="{{ old('fecha_entrega', $pedido->fecha_entrega) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                    @error('fecha_entrega')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado *</label>
                    <select name="estado" id="estado" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                        @php
                            $estados = [
                                'Cotización' => 'Cotización',
                                'Solicitud' => 'Solicitud',
                                'Confirmado' => 'Confirmado',
                                'En Curso' => 'En Curso',
                                'Listo para Retirar' => 'Listo para Retirar',
                                'Entregado' => 'Entregado',
                                'Cancelado' => 'Cancelado'
                            ];
                            
                            $estadoActual = old('estado', $pedido->estado);
                            $estadosPermitidos = [];
                            
                            // Lógica de estados permitidos
                            switch($estadoActual) {
                                case 'Solicitud':
                                    $estadosPermitidos = ['Solicitud', 'Cotización', 'Cancelado'];
                                    break;
                                case 'Cotización':
                                    $estadosPermitidos = ['Cotización', 'Confirmado', 'Cancelado'];
                                    break;
                                case 'Confirmado':
                                    $estadosPermitidos = ['Confirmado', 'En Curso', 'Cancelado'];
                                    break;
                                case 'En Curso':
                                    $estadosPermitidos = ['En Curso', 'Listo para Retirar', 'Cancelado'];
                                    break;
                                case 'Listo para Retirar':
                                    $estadosPermitidos = ['Listo para Retirar', 'Entregado', 'Cancelado'];
                                    break;
                                case 'Entregado':
                                    $estadosPermitidos = ['Entregado']; // No se puede cambiar
                                    break;
                                case 'Cancelado':
                                    $estadosPermitidos = ['Cancelado']; // No se puede cambiar
                                    break;
                                default:
                                    $estadosPermitidos = array_keys($estados);
                            }
                        @endphp
                        
                        @foreach($estados as $valor => $etiqueta)
                            @if(in_array($valor, $estadosPermitidos))
                                <option value="{{ $valor }}" {{ $estadoActual == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700">Notas</label>
                    <textarea name="notas" id="notas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">{{ old('notas', $pedido->notas) }}</textarea>
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
                @if($pedido->items && $pedido->items->count() > 0)
                    @foreach($pedido->items as $index => $item)
                        <div class="producto-item border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="text-md font-medium text-gray-900">Producto {{ $index + 1 }}</h4>
                                <button type="button" class="eliminar-producto text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div>
                                    <label class="block text-sm font-medium text-gray-700">Producto *</label>
                                    <select name="items[{{ $index }}][producto_id]" class="producto-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
                                        <option value="">Seleccionar producto</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id }}" 
                                                    data-categoria="{{ $producto->categoria }}"
                                                    data-maquina="{{ $producto->maquina ? $producto->maquina->nombre : '' }}"
                                                    data-tipo="{{ $producto->tipo_impresion }}"
                                                    data-insumo="{{ $producto->insumo ? $producto->insumo->nombre : '' }}"
                                                    data-precio="{{ $producto->precio_venta }}"
                                                    {{ $item->producto_id == $producto->id ? 'selected' : '' }}>
                                                {{ $producto->nombre }} - {{ ucfirst($producto->categoria) }}
                                                @if($producto->maquina) - {{ $producto->maquina->nombre }} @endif
                                                @if($producto->tipo_impresion) - {{ ucfirst($producto->tipo_impresion) }} @endif
                                                @if($producto->insumo) - {{ $producto->insumo->nombre }} @endif
                                                - ${{ number_format($producto->precio_venta, 2) }}
                    </option>
                @endforeach
            </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cantidad *</label>
                                    <input type="number" name="items[{{ $index }}][cantidad]" min="1" value="{{ $item->cantidad }}" class="cantidad-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descuento (%)</label>
                                    <input type="number" name="items[{{ $index }}][descuento_item]" min="0" max="100" value="{{ $item->descuento_item }}" class="descuento-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="items[{{ $index }}][es_doble_faz]" value="1" {{ $item->es_doble_faz ? 'checked' : '' }} class="doble-faz-checkbox rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span class="ml-2 text-sm text-gray-700">Doble faz</span>
                                    </label>
                                </div>

                                <div class="lg:col-span-2">
                                    <div class="producto-info text-sm text-gray-600">
                                        @if($item->producto)
                                            <strong>Categoría:</strong> {{ ucfirst($item->producto->categoria) }}
                                            @if($item->producto->maquina)
                                                <br><strong>Máquina:</strong> {{ $item->producto->maquina->nombre }}
                                            @endif
                                            @if($item->producto->tipo_impresion)
                                                <br><strong>Tipo:</strong> {{ strtoupper($item->producto->tipo_impresion) }}
                                            @endif
                                            @if($item->producto->insumo)
                                                <br><strong>Insumo:</strong> {{ $item->producto->insumo->nombre }}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-gray-50 rounded-md">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Total del ítem:</span>
                                    <span class="text-lg font-semibold text-gray-900 item-total">${{ number_format($item->precio_venta_item, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
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
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800">
                Actualizar Pedido
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div>
                <label class="block text-sm font-medium text-gray-700">Producto *</label>
                <select name="items[INDEX][producto_id]" class="producto-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
                    <option value="">Seleccionar producto</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" 
                                data-categoria="{{ $producto->categoria }}"
                                data-maquina="{{ $producto->maquina ? $producto->maquina->nombre : '' }}"
                                data-tipo="{{ $producto->tipo_impresion }}"
                                data-insumo="{{ $producto->insumo ? $producto->insumo->nombre : '' }}"
                                data-precio="{{ $producto->precio_venta }}">
                            {{ $producto->nombre }} - {{ ucfirst($producto->categoria) }}
                            @if($producto->maquina) - {{ $producto->maquina->nombre }} @endif
                            @if($producto->tipo_impresion) - {{ ucfirst($producto->tipo_impresion) }} @endif
                            @if($producto->insumo) - {{ $producto->insumo->nombre }} @endif
                            - ${{ number_format($producto->precio_venta, 2) }}
                        </option>
                    @endforeach
            </select>
        </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Cantidad *</label>
                <input type="number" name="items[INDEX][cantidad]" min="1" value="1" class="cantidad-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900" required>
            </div>

        <div>
                <label class="block text-sm font-medium text-gray-700">Descuento (%)</label>
                <input type="number" name="items[INDEX][descuento_item]" min="0" max="100" value="0" class="descuento-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-900 focus:border-gray-900">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                <div>
                <label class="flex items-center">
                    <input type="checkbox" name="items[INDEX][es_doble_faz]" value="1" class="doble-faz-checkbox rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                    <span class="ml-2 text-sm text-gray-700">Doble faz</span>
                </label>
            </div>

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
    let productoIndex = {{ $pedido->items ? $pedido->items->count() : 0 }};
    const productosContainer = document.getElementById('productos-container');
    const agregarProductoBtn = document.getElementById('agregar-producto');
    const productoTemplate = document.getElementById('producto-template');

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

    // Funciones para productos
    agregarProductoBtn.addEventListener('click', agregarProducto);

    function agregarProducto() {
        const productoElement = productoTemplate.content.cloneNode(true);
        const index = productoIndex++;
        
        // Reemplazar INDEX con el índice real
        productoElement.innerHTML = productoElement.innerHTML.replace(/INDEX/g, index);
        
        // Actualizar número del producto
        productoElement.querySelector('.producto-numero').textContent = productoIndex;
        
        // Agregar event listeners
        const productoSelect = productoElement.querySelector('.producto-select');
        const cantidadInput = productoElement.querySelector('.cantidad-input');
        const descuentoInput = productoElement.querySelector('.descuento-input');
        const dobleFazCheckbox = productoElement.querySelector('.doble-faz-checkbox');
        const eliminarBtn = productoElement.querySelector('.eliminar-producto');
        
        productoSelect.addEventListener('change', () => actualizarProductoInfo(productoElement));
        cantidadInput.addEventListener('input', () => calcularTotal(productoElement));
        descuentoInput.addEventListener('input', () => calcularTotal(productoElement));
        dobleFazCheckbox.addEventListener('change', () => calcularTotal(productoElement));
        eliminarBtn.addEventListener('click', () => eliminarProducto(productoElement));
        
        productosContainer.appendChild(productoElement);
    }

    // Función para cargar máquinas
    function cargarMaquinas(productoElement) {
        const maquinaSelect = productoElement.querySelector('.maquina-select');
        const categoriaSelect = productoElement.querySelector('.categoria-select');
        
        // Obtener todas las máquinas disponibles
        const maquinas = @json($maquinas);
        
        // Limpiar opciones existentes
        maquinaSelect.innerHTML = '<option value="">Seleccionar máquina</option>';
        
        maquinas.forEach(maquina => {
            const option = document.createElement('option');
            option.value = maquina.id;
            option.textContent = maquina.nombre;
            option.dataset.color = maquina.costo_color_carilla ? 'true' : 'false';
            maquinaSelect.appendChild(option);
        });
        
        // Remover event listeners existentes para evitar duplicados
        const newMaquinaSelect = maquinaSelect.cloneNode(true);
        maquinaSelect.parentNode.replaceChild(newMaquinaSelect, maquinaSelect);
        
        // Event listener para máquina
        newMaquinaSelect.addEventListener('change', function() {
            console.log('Máquina seleccionada:', newMaquinaSelect.value);
            const tipoSelect = productoElement.querySelector('.tipo-select');
            const productoSelect = productoElement.querySelector('.producto-select');
            
            tipoSelect.disabled = false;
            productoSelect.disabled = true;
            
            // Limpiar tipo y producto
            tipoSelect.innerHTML = '<option value="">Seleccionar tipo</option>';
            productoSelect.innerHTML = '<option value="">Seleccionar producto</option>';
            
            // Agregar opciones de tipo
            tipoSelect.innerHTML += '<option value="b&n">Blanco y Negro</option>';
            
            const maquinaSeleccionada = newMaquinaSelect.selectedOptions[0];
            if (maquinaSeleccionada && maquinaSeleccionada.dataset.color === 'true') {
                tipoSelect.innerHTML += '<option value="color">Color</option>';
            }
            
            // Si es BH227, seleccionar automáticamente B&N
            if (maquinaSeleccionada && maquinaSeleccionada.textContent.toLowerCase().includes('bh227')) {
                // Esperar un poco para que se actualice el DOM
                setTimeout(() => {
                    tipoSelect.value = 'b&n';
                    // Disparar el evento change para cargar productos
                    const changeEvent = new Event('change', { bubbles: true });
                    tipoSelect.dispatchEvent(changeEvent);
                }, 100);
            }
            
            // Remover event listeners existentes para tipo
            const newTipoSelect = tipoSelect.cloneNode(true);
            tipoSelect.parentNode.replaceChild(newTipoSelect, tipoSelect);
            
            // Event listener para tipo
            newTipoSelect.addEventListener('change', function() {
                console.log('Tipo seleccionado:', newTipoSelect.value);
                const productoSelect = productoElement.querySelector('.producto-select');
                productoSelect.disabled = false;
                filtrarProductos(productoElement, categoriaSelect.value, newMaquinaSelect.value, newTipoSelect.value);
            });
        });
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
        
        // Calcular cantidad de hojas para productos de impresión/fotocopia
        let cantidadHojas = cantidad;
        if (esDobleFaz && ['impresion', 'fotocopia'].includes(option.dataset.categoria)) {
            cantidadHojas = Math.ceil(cantidad / 2);
        }
        
        const subtotal = precioBase * cantidadHojas;
        const montoDescuento = subtotal * (descuento / 100);
        const total = subtotal - montoDescuento;
        
        totalSpan.textContent = `$${total.toFixed(2)}`;
    }

    function eliminarProducto(productoElement) {
        productoElement.remove();
    }

    // Inicializar event listeners para productos existentes
    document.querySelectorAll('.producto-item').forEach(item => {
        const productoSelect = item.querySelector('.producto-select');
        const cantidadInput = item.querySelector('.cantidad-input');
        const descuentoInput = item.querySelector('.descuento-input');
        const dobleFazCheckbox = item.querySelector('.doble-faz-checkbox');
        const eliminarBtn = item.querySelector('.eliminar-producto');
        
        if (productoSelect) productoSelect.addEventListener('change', () => actualizarProductoInfo(item));
        if (cantidadInput) cantidadInput.addEventListener('input', () => calcularTotal(item));
        if (descuentoInput) descuentoInput.addEventListener('input', () => calcularTotal(item));
        if (dobleFazCheckbox) dobleFazCheckbox.addEventListener('change', () => calcularTotal(item));
        if (eliminarBtn) eliminarBtn.addEventListener('click', () => {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                eliminarProducto(item);
            }
        });
    });
});
</script>
@endsection
