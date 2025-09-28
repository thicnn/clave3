<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido</title>
</head>
<body>
    <h1>Editar Pedido #{{ $pedido->id }}</h1>

    {{-- Preparamos un array para acceder fácilmente a los insumos del pedido --}}
    @php
        $insumosDelPedido = $pedido->insumos->pluck('pivot.cantidad', 'id')->all();
    @endphp

    <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="cliente_id">Cliente:</label>
            <select name="cliente_id" id="cliente_id" required>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('cliente_id', $pedido->cliente_id) == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
            @error('cliente_id')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="fecha_entrega">Fecha de Entrega (Opcional):</label>
            <input type="date" id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega', $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('Y-m-d') : '') }}">
        </div>
        <br>
        <div>
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <option value="Cotización" {{ old('estado', $pedido->estado) == 'Cotización' ? 'selected' : '' }}>Cotización</option>
                <option value="Confirmado" {{ old('estado', $pedido->estado) == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                <option value="Listo para Retirar" {{ old('estado', $pedido->estado) == 'Listo para Retirar' ? 'selected' : '' }}>Listo para Retirar</option>
                <option value="Entregado" {{ old('estado', $pedido->estado) == 'Entregado' ? 'selected' : '' }}>Entregado</option>
            </select>
        </div>
        <br>
        <div>
            <label for="total">Total (Opcional):</label>
            <input type="number" id="total" name="total" step="0.01" min="0" value="{{ old('total', $pedido->total) }}">
            @error('total')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <hr>
        <h3>Insumos para el Pedido</h3>
        <div id="insumos-list">
            @foreach ($insumos as $insumo)
                <div>
                    <input type="checkbox" 
                           name="insumos[{{ $insumo->id }}][id]" 
                           value="{{ $insumo->id }}"
                           {{-- Marcamos el checkbox si el insumo ya está en el pedido --}}
                           {{ isset($insumosDelPedido[$insumo->id]) ? 'checked' : '' }}>

                    <label>{{ $insumo->nombre }} (Stock: {{ $insumo->stock_actual }})</label>
                    
                    <input type="number" 
                           name="insumos[{{ $insumo->id }}][cantidad]" 
                           min="1" 
                           placeholder="Cantidad" 
                           style="width: 80px;"
                           {{-- Ponemos la cantidad si el insumo ya está en el pedido --}}
                           value="{{ $insumosDelPedido[$insumo->id] ?? '' }}">
                </div>
            @endforeach
        </div>
        <hr>
        <br>
        <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST" enctype="multipart/form-data">
        <br>
<hr>
<h3>Archivos Adjuntos</h3>
@if ($pedido->archivos->count() > 0)
    <ul>
        @foreach ($pedido->archivos as $archivo)
            <li>
                <a href="{{ asset('storage/' . $archivo->ruta) }}" target="_blank">
                    {{ $archivo->nombre_original }}
                </a>
                {{-- En el futuro aquí pondremos un botón para eliminar archivos individuales --}}
            </li>
        @endforeach
    </ul>
@else
    <p>No hay archivos adjuntos para este pedido.</p>
@endif

<br>

<div>
    <label for="archivos">Añadir Nuevos Archivos:</label>
    <input type="file" id="archivos" name="archivos[]" multiple>
    @error('archivos.*')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>
        <button type="submit">Actualizar Pedido</button>
    </form>
    <br>
    <a href="{{ route('pedidos.index') }}">Cancelar y Volver</a>
</body>
</html>