<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Insumos</title>
</head>
<body>
    <h1>Inventario de Insumos</h1>

    @if (session('success'))
        <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('insumos.create') }}">+ Añadir Nuevo Insumo</a>

    <hr>

    <table border="1" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Costo</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($insumos as $insumo)
                <tr>
                    <td>{{ $insumo->id }}</td>
                    <td>{{ $insumo->nombre }}</td>
                    <td>$ {{ number_format($insumo->costo, 2) }}</td>
                    <td>{{ $insumo->stock_actual }}</td>
                    <td>{{ $insumo->stock_minimo }}</td>
                    

<td>
    <a href="{{ route('insumos.edit', $insumo->id) }}">Editar</a>
    <form action="{{ route('insumos.destroy', $insumo->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este insumo?')">Eliminar</button>
    </form>
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay insumos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>