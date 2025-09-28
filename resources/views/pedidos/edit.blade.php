<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido</title>
</head>
<body>
    <h1>Editar Pedido #{{ $pedido->id }}</h1>

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
        <br>
        <button type="submit">Actualizar Pedido</button>
    </form>
    <hr>
    <a href="{{ route('pedidos.index') }}">Cancelar y Volver</a>
</body>
</html>