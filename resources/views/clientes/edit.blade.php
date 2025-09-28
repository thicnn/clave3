<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
</head>
<body>
    <h1>Editar Cliente: {{ $cliente->nombre }}</h1>

    <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required>
            @error('nombre')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}" required>
            @error('email')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}">
            @error('telefono')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="rut">RUT:</label>
            <input type="number" id="rut" name="rut" value="{{ old('rut', $cliente->rut) }}">
            @error('rut')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="direccion">Dirección:</label>
            <textarea id="direccion" name="direccion">{{ old('direccion', $cliente->direccion) }}</textarea>
        </div>
        <br>
        <button type="submit">Actualizar Cliente</button>
    </form>
    <hr>
    <a href="{{ route('clientes.index') }}">Cancelar y Volver a la lista</a>
</body>
</html>