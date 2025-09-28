<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Insumo</title>
</head>
<body>
    <h1>Editar Insumo: {{ $insumo->nombre }}</h1>

    <form action="{{ route('insumos.update', $insumo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="nombre">Nombre del Insumo:</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $insumo->nombre) }}" required>
            @error('nombre')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion">{{ old('descripcion', $insumo->descripcion) }}</textarea>
        </div>
        <br>
        <div>
            <label for="costo">Costo (por unidad):</label>
            <input type="number" id="costo" name="costo" step="0.01" min="0" value="{{ old('costo', $insumo->costo) }}" required>
            @error('costo')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="stock_actual">Stock Actual:</label>
            <input type="number" id="stock_actual" name="stock_actual" min="0" value="{{ old('stock_actual', $insumo->stock_actual) }}" required>
            @error('stock_actual')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="stock_minimo">Stock Mínimo:</label>
            <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="{{ old('stock_minimo', $insumo->stock_minimo) }}" required>
            @error('stock_minimo')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <button type="submit">Actualizar Insumo</button>
    </form>
    <hr>
    <a href="{{ route('insumos.index') }}">Cancelar y Volver</a>
</body>
</html>