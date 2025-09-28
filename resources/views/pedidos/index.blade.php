@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Listado de Pedidos</h1>
        <a href="{{ route('pedidos.create') }}" class="btn btn-primary">+ Registrar Nuevo Pedido</a>
    </div>

    {{-- Formulario de Búsqueda y Filtro --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pedidos.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label for="search_cliente" class="visually-hidden">Buscar por Cliente</label>
                    <input type="text" class="form-control" id="search_cliente" name="search_cliente" placeholder="Buscar por nombre de cliente..." value="{{ request('search_cliente') }}">
                </div>
                <div class="col-md-5">
                    <label for="filter_estado" class="visually-hidden">Filtrar por Estado</label>
                    <select class="form-select" id="filter_estado" name="filter_estado">
                        <option value="">-- Todos los estados --</option>
                        <option value="Cotización" {{ request('filter_estado') == 'Cotización' ? 'selected' : '' }}>Cotización</option>
                        <option value="Confirmado" {{ request('filter_estado') == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="Listo para Retirar" {{ request('filter_estado') == 'Listo para Retirar' ? 'selected' : '' }}>Listo para Retirar</option>
                        <option value="Entregado" {{ request('filter_estado') == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
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
                    <td><a href="{{ route('pedidos.show', $pedido->id) }}">#{{ $pedido->id }}</a></td>
                    <td>{{ $pedido->cliente->nombre }}</td>
                    <td>{{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $pedido->estado }}</td>
                    <td>$ {{ number_format($pedido->total, 2) }}</td>
                    <td>
                        <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este pedido?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No se encontraron pedidos que coincidan con la búsqueda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection