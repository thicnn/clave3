<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Pedidos</title>
</head>
<body>
    <h1>Listado de Pedidos</h1>

    <a href="{{ route('pedidos.create') }}">+ Registrar Nuevo Pedido</a>

    <hr>

    <table border="1" style="width:100%">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Fecha Entrega</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pedidos as $pedido)
                <tr>
                    <td><a href="{{ route('pedidos.show', $pedido->id) }}">{{ $pedido->id }}</a></td>
                    <td>{{ $pedido->cliente->nombre }}</td>
                    <td>{{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $pedido->estado }}</td>
                    <td>$ {{ number_format($pedido->total, 2) }}</td>
                    <td>
    <a href="{{ route('pedidos.edit', $pedido->id) }}">Ver/Editar</a>

    <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este pedido?')">Eliminar</button>
    </form>
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay pedidos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>