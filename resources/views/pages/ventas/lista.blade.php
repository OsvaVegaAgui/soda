@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    #tablaVentas thead { background: #6f42c1; color: #fff; }
    .badge-metodo { font-size: .75rem; padding: .35em .65em; }
</style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">

            <div class="card-header justify-content-between">
                <div class="card-title">
                    <i class="bi bi-receipt me-2"></i>
                    {{ auth()->user()->rol === 1 ? 'Historial de Ventas' : 'Mis Ventas' }}
                </div>
                <a href="{{ route('ventas', ['accion' => 'cobrar']) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-cart-plus me-1"></i>Nueva Venta
                </a>
            </div>

            <div class="card-body">

                {{-- Filtro de fechas --}}
                <form method="GET" action="{{ route('ventas', ['accion' => 'lista']) }}"
                      class="d-flex flex-wrap align-items-end gap-3 mb-4">
                    <div>
                        <label class="form-label fw-semibold text-muted small text-uppercase mb-1">Desde</label>
                        <input type="date" name="fecha_ini" value="{{ $fechaIni }}" class="form-control" style="width:180px;">
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-muted small text-uppercase mb-1">Hasta</label>
                        <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control" style="width:180px;">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('ventas', ['accion' => 'lista']) }}" class="btn btn-outline-secondary">
                        Hoy
                    </a>
                </form>

                {{-- Resumen rápido --}}
                @if($ventas->isNotEmpty())
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="p-3 bg-primary-transparent rounded text-center">
                            <div class="fs-4 fw-bold">{{ $ventas->count() }}</div>
                            <div class="text-muted small">Ventas</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-success-transparent rounded text-center">
                            <div class="fs-4 fw-bold">₡{{ number_format($ventas->sum('total'), 2) }}</div>
                            <div class="text-muted small">Total facturado</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-warning-transparent rounded text-center">
                            <div class="fs-4 fw-bold">₡{{ number_format($ventas->where('metodo_pago', 'efectivo')->sum('total') + $ventas->where('metodo_pago', 'mixto')->sum(fn($v) => $v->monto_efectivo ?? 0), 2) }}</div>
                            <div class="text-muted small">En efectivo</div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tabla --}}
                <div class="table-responsive">
                    <table id="tablaVentas" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                @if(auth()->user()->rol === 1)
                                <th>Cajera</th>
                                @endif
                                <th class="text-center">Ítems</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Pago</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventas as $venta)
                            <tr>
                                <td class="text-muted small">{{ $venta->id }}</td>
                                <td>{{ $venta->fecha ? $venta->fecha->format('d/m/Y') : '—' }}</td>
                                @if(auth()->user()->rol === 1)
                                <td>{{ $venta->user ? $venta->user->name : '—' }}</td>
                                @endif
                                <td class="text-center">{{ $venta->detalles->count() }}</td>
                                <td class="text-end fw-semibold">₡{{ number_format($venta->total ?? $venta->detalles->sum('subtotal'), 2) }}</td>
                                <td class="text-center">
                                    @php $mp = $venta->metodo_pago; @endphp
                                    @if($mp === 'efectivo')
                                        <span class="badge bg-success badge-metodo"><i class="bi bi-cash me-1"></i>Efectivo</span>
                                    @elseif($mp === 'tarjeta')
                                        <span class="badge bg-info badge-metodo"><i class="bi bi-credit-card me-1"></i>Tarjeta</span>
                                    @elseif($mp === 'mixto')
                                        <span class="badge bg-warning text-dark badge-metodo"><i class="bi bi-wallet2 me-1"></i>Mixto</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('ventas', ['accion' => 'ver', 'id' => $venta->id]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->rol === 1 ? 7 : 6 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;"></i>
                                    No hay ventas en el rango de fechas seleccionado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#tablaVentas').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: -1 }]
    });
});
</script>
@endsection
