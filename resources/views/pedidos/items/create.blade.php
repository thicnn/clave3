@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Agregar Ítem al Pedido #{{ $pedido->id }}</h1>
        <p class="mt-2 text-sm text-gray-600">Agrega un nuevo ítem al pedido</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <form id="itemForm" action="{{ route('pedidos.items.store', $pedido->id) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Categoría -->
            <div>
                <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                <select id="categoria" name="categoria" required onchange="toggleFields()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                    <option value="">Seleccionar categoría</option>
                    <option value="servicio">Servicio</option>
                    <option value="impresion">Impresión</option>
                    <option value="fotocopia">Fotocopia</option>
                </select>
            </div>

            <!-- Campos para Servicio -->
            <div id="servicioFields" class="hidden space-y-4">
                <div>
                    <label for="descripcion_servicio" class="block text-sm font-medium text-gray-700 mb-1">Descripción del Servicio *</label>
                    <input type="text" id="descripcion_servicio" name="descripcion_servicio"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                </div>
                <div>
                    <label for="precio_manual" class="block text-sm font-medium text-gray-700 mb-1">Precio Manual *</label>
                    <input type="number" id="precio_manual" name="precio_manual" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                </div>
            </div>

            <!-- Campos para Impresión/Fotocopia -->
            <div id="impresionFields" class="hidden space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tipo_impresion" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Impresión *</label>
                        <select id="tipo_impresion" name="tipo_impresion" onchange="updateMaquinas()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                            <option value="">Seleccionar tipo</option>
                            <option value="b&n">Blanco y Negro</option>
                            <option value="color">Color</option>
                        </select>
                    </div>
                    <div>
                        <label for="maquina_id" class="block text-sm font-medium text-gray-700 mb-1">Máquina *</label>
                        <select id="maquina_id" name="maquina_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                            <option value="">Seleccionar máquina</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="insumo_id" class="block text-sm font-medium text-gray-700 mb-1">Insumo (Papel) *</label>
                    <select id="insumo_id" name="insumo_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                        <option value="">Seleccionar insumo</option>
                        @foreach($insumos as $insumo)
                            <option value="{{ $insumo['id'] }}" data-costo="{{ $insumo['costo_por_unidad'] }}">
                                {{ $insumo['nombre'] }} - ${{ number_format($insumo['costo_por_unidad'], 2) }}/hoja
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Campos comunes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required onchange="calcularPrecio()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                </div>
                <div>
                    <label for="descuento_item" class="block text-sm font-medium text-gray-700 mb-1">Descuento (%)</label>
                    <input type="number" id="descuento_item" name="descuento_item" min="0" max="100" step="0.01" value="0" onchange="calcularPrecio()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500">
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" id="es_doble_faz" name="es_doble_faz" value="1" onchange="calcularPrecio()"
                           class="rounded border-gray-300 text-gray-600 shadow-sm focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Doble faz</span>
                </label>
            </div>

            <!-- Resultado del cálculo -->
            <div id="precioResult" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Precio de Venta:</p>
                        <p class="text-sm text-gray-500">Costo de Producción:</p>
                    </div>
                    <div class="text-right">
                        <p id="precioVenta" class="text-lg font-semibold text-gray-900">$0.00</p>
                        <p id="costoProduccion" class="text-sm text-gray-500">$0.00</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('pedidos.show', $pedido->id) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800">
                    Agregar Ítem
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Datos de máquinas
const maquinas = @json($maquinas);

function toggleFields() {
    const categoria = document.getElementById('categoria').value;
    const servicioFields = document.getElementById('servicioFields');
    const impresionFields = document.getElementById('impresionFields');
    
    if (categoria === 'servicio') {
        servicioFields.classList.remove('hidden');
        impresionFields.classList.add('hidden');
    } else if (categoria === 'impresion' || categoria === 'fotocopia') {
        servicioFields.classList.add('hidden');
        impresionFields.classList.remove('hidden');
    } else {
        servicioFields.classList.add('hidden');
        impresionFields.classList.add('hidden');
    }
    
    calcularPrecio();
}

function updateMaquinas() {
    const tipoImpresion = document.getElementById('tipo_impresion').value;
    const maquinaSelect = document.getElementById('maquina_id');
    
    // Limpiar opciones
    maquinaSelect.innerHTML = '<option value="">Seleccionar máquina</option>';
    
    // Filtrar máquinas según el tipo
    maquinas.forEach(maquina => {
        if (tipoImpresion === 'color' && !maquina.soporta_color) {
            return;
        }
        
        const option = document.createElement('option');
        option.value = maquina.id;
        option.textContent = maquina.nombre;
        option.dataset.costoBn = maquina.costo_bn;
        option.dataset.costoColor = maquina.costo_color;
        maquinaSelect.appendChild(option);
    });
    
    calcularPrecio();
}

function calcularPrecio() {
    const categoria = document.getElementById('categoria').value;
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    const descuento = parseFloat(document.getElementById('descuento_item').value) || 0;
    const esDobleFaz = document.getElementById('es_doble_faz').checked;
    
    if (categoria === '' || cantidad === 0) {
        document.getElementById('precioResult').classList.add('hidden');
        return;
    }
    
    let costoProduccion = 0;
    let precioVenta = 0;
    
    if (categoria === 'servicio') {
        const precioManual = parseFloat(document.getElementById('precio_manual').value) || 0;
        costoProduccion = precioManual / 2; // Estimación
        precioVenta = precioManual;
    } else {
        const maquinaId = document.getElementById('maquina_id').value;
        const insumoId = document.getElementById('insumo_id').value;
        const tipoImpresion = document.getElementById('tipo_impresion').value;
        
        if (maquinaId && insumoId) {
            const maquina = maquinas.find(m => m.id == maquinaId);
            const insumoOption = document.querySelector(`#insumo_id option[value="${insumoId}"]`);
            
            if (maquina && insumoOption) {
                const costoInsumo = parseFloat(insumoOption.dataset.costo);
                const costoImpresion = tipoImpresion === 'color' ? maquina.costo_color : maquina.costo_bn;
                
                const cantidadHojas = esDobleFaz ? Math.ceil(cantidad / 2) : cantidad;
                
                costoProduccion = (cantidadHojas * costoInsumo) + (cantidad * costoImpresion);
                precioVenta = costoProduccion * 2; // Margen del 100%
            }
        }
    }
    
    // Aplicar descuento
    const montoDescuento = precioVenta * (descuento / 100);
    precioVenta = precioVenta - montoDescuento;
    
    // Mostrar resultados
    document.getElementById('precioVenta').textContent = '$' + precioVenta.toFixed(2);
    document.getElementById('costoProduccion').textContent = '$' + costoProduccion.toFixed(2);
    document.getElementById('precioResult').classList.remove('hidden');
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>
@endsection
