<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
</head>
<body>
    <h1>Listado de Clientes</h1>

    @if (session('success'))
        <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('clientes.create') }}">+ Añadir Nuevo Cliente</a>

    <hr>

    <table border="1" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id }}</td>
                    <td>{{ $cliente->nombre }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>
                        <a href="{{ route('clientes.edit', $cliente->id) }}">Editar</a>
                        <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar a este cliente?')">Eliminar</button>
</form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay clientes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>