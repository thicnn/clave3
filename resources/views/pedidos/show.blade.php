<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido #{{ $pedido->id }}</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .card { border: 1px solid #eee; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .card-header { font-weight: bold; font-size: 1.2em; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .list-group { list-style: none; padding: 0; }
        .list-group-item { padding: 8px; border: 1px solid #ddd; border-bottom: none; }
        .list-group-item:last-child { border-bottom: 1px solid #ddd; }
        .btn { padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Detalle del Pedido #{{ $pedido->id }}</h1>
    <p>
        <a href="{{ route('pedidos.index') }}" class="btn">Volver al Listado</a>
        <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn" style="background-color: #28a745;">Editar Pedido</a>
    </p>

    <div class="card">
        <div class="card-header">Datos del Pedido</div>
        <table class="table">
            <tr>
                <th>Cliente</th>
                <td>{{ $pedido->cliente->nombre }} ({{ $pedido->cliente->email }})</td>
            </tr>
            <tr>
                <th>Fecha de Entrega</th>
                <td>{{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') : 'No especificada' }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ $pedido->estado }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <td>$ {{ number_format($pedido->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="card-header">Insumos Utilizados</div>
        @if($pedido->insumos->isNotEmpty())
            <table class="table">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Cantidad Utilizada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedido->insumos as $insumo)
                        <tr>
                            <td>{{ $insumo->nombre }}</td>
                            <td>{{ $insumo->pivot->cantidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No se utilizaron insumos para este pedido.</p>
        @endif
    </div>

    <div class="card">
        <div class="card-header">Archivos Adjuntos</div>
        @if($pedido->archivos->isNotEmpty())
            <ul class="list-group">
                @foreach ($pedido->archivos as $archivo)
                    <li class="list-group-item">
                        <a href="{{ asset('storage/' . $archivo->ruta) }}" target="_blank">
                            {{ $archivo->nombre_original }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No hay archivos adjuntos para este pedido.</p>
        @endif
    </div>
</div>

</body>
</html>