<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido #{{ $pedido->id }}</title>
</head>
<body>
    <h1>Detalle del Pedido #{{ $pedido->id }}</h1>

    <p><strong>Cliente:</strong> {{ $pedido->cliente->nombre }}</p>
    <p><strong>Estado:</strong> {{ $pedido->estado }}</p>
    <p><strong>Fecha de Entrega:</strong> {{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') : 'No definida' }}</p>
    <p><strong>Total:</strong> $ {{ $pedido->total ? number_format($pedido->total, 2) : 'No definido' }}</p>

    <hr>

    <h3>Insumos Utilizados</h3>
    <ul>
        @forelse ($pedido->insumos as $insumo)
            <li>
                {{ $insumo->nombre }} - <strong>Cantidad:</strong> {{ $insumo->pivot->cantidad }}
            </li>
        @empty
            <li>No hay insumos asignados a este pedido.</li>
        @endforelse
    </ul>

    <br>
    <a href="{{ route('pedidos.index') }}">Volver a la lista</a>
    <a href="{{ route('pedidos.edit', $pedido->id) }}">Editar este Pedido</a>
</body>
</html>