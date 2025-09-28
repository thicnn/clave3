@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- Alertas de Stock --}}
        <div class="col-md-12 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i> Alertas de Stock Bajo
                </div>
                <div class="card-body">
                    @if($alertasStock->isNotEmpty())
                        <ul class="list-group">
                            @foreach ($alertasStock as $insumo)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('insumos.edit', $insumo->id) }}">{{ $insumo->nombre }}</a>
                                    <span class="badge bg-danger rounded-pill">
                                        Stock actual: {{ $insumo->stock_actual }} (Mínimo: {{ $insumo->stock_minimo }})
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-success mb-0"><i class="fas fa-check-circle"></i> ¡No hay problemas de stock!</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pedidos para Entregar --}}
        <div class="col-md-6">
            <div class="card border-info mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-box-open"></i> Pedidos para Entregar
                </div>
                <div class="card-body">
                    @if($pedidosParaEntregar->isNotEmpty())
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Fecha Entrega</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedidosParaEntregar as $pedido)
                                    <tr>
                                        <td>#{{ $pedido->id }}</td>
                                        <td>{{ $pedido->cliente->nombre }}</td>
                                        <td>{{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No hay pedidos listos para entregar.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Últimos Pedidos Recientes --}}
        <div class="col-md-6">
            <div class="card border-secondary mb-4">
                <div class="card-header bg-secondary text-white">
                   <i class="fas fa-history"></i> Pedidos Recientes
                </div>
                <div class="card-body">
                     @if($pedidosRecientes->isNotEmpty())
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedidosRecientes as $pedido)
                                    <tr>
                                        <td>#{{ $pedido->id }}</td>
                                        <td>{{ $pedido->cliente->nombre }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $pedido->estado }}</span></td>
                                        <td>
                                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                         <p class="text-muted mb-0">No se han registrado pedidos recientemente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection