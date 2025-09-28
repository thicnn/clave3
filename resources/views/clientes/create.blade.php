<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Cliente</title>
</head>
<body>
    <h1>Añadir Nuevo Cliente</h1>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            @error('nombre')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}">
            @error('telefono')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="rut">RUT:</label>
            <input type="number" id="rut" name="rut" value="{{ old('rut') }}">
            @error('rut')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="direccion">Dirección:</label>
            <textarea id="direccion" name="direccion">{{ old('direccion') }}</textarea>
        </div>
        <br>
        <button type="submit">Guardar Cliente</button>
    </form>
    <hr>
    <a href="{{ route('clientes.index') }}">Volver a la lista</a>
</body>
</html>