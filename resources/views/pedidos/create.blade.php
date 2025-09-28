<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Registrar Nuevo Pedido</title>
</head>
<body>
    <h1>Registrar Nuevo Pedido</h1>

    <form action="{{ route('pedidos.store') }}" method="POST">
        @csrf

        <div>
            <label for="cliente_id">Cliente:</label>
            <select name="cliente_id" id="cliente_id" required>
                <option value="">-- Seleccione un Cliente --</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
            @error('cliente_id')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        
<hr>
<h3>Insumos para el Pedido</h3>
<div id="insumos-list">
    @foreach ($insumos as $insumo)
        <div>
            <input type="checkbox" name="insumos[{{ $insumo->id }}][id]" value="{{ $insumo->id }}">
            <label>{{ $insumo->nombre }} (Stock: {{ $insumo->stock_actual }})</label>
            <input type="number" name="insumos[{{ $insumo->id }}][cantidad]" min="1" placeholder="Cantidad" style="width: 80px;">
        </div>
    @endforeach
</div>
@error('insumos')
    <div style="color: red;">{{ $message }}</div>
@enderror
<hr>
<br>
        <div>
            <label for="fecha_entrega">Fecha de Entrega (Opcional):</label>
            <input type="date" id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega') }}">
        </div>
        <br>
        <div>
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <option value="Cotización" {{ old('estado', 'Cotización') == 'Cotización' ? 'selected' : '' }}>Cotización</option>
                <option value="Confirmado" {{ old('estado') == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                <option value="Listo para Retirar" {{ old('estado') == 'Listo para Retirar' ? 'selected' : '' }}>Listo para Retirar</option>
                <option value="Entregado" {{ old('estado') == 'Entregado' ? 'selected' : '' }}>Entregado</option>
            </select>
        </div>
        <br>
        <div>
            <label for="total">Total (Opcional):</label>
            <input type="number" id="total" name="total" step="0.01" min="0" value="{{ old('total') }}">
            @error('total')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <button type="submit">Guardar Pedido</button>
    </form>
    <hr>
    <a href="#">Cancelar y Volver</a>
</body>
</html>