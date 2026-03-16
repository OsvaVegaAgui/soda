@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')

{{-- Filtros --}}
<div class="card custom-card mb-3">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-funnel me-2"></i>Filtros del Reporte</div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('ventas', ['accion' => 'reporte']) }}">
            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha inicio</label>
                    <input type="date" name="fecha_ini" class="form-control"
                           value="{{ $filtros['fecha_ini'] }}" max="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control"
                           value="{{ $filtros['fecha_fin'] }}" max="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Usuario</label>
                    <select name="user_id" class="form-select">
                        <option value="">— Todos —</option>
                        @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ $filtros['user_id'] == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de producto</label>
                    <select name="tipo_producto" class="form-select">
                        <option value="">— Todos —</option>
                        <option value="soda"        {{ ($filtros['tipo_producto'] ?? '') === 'soda'        ? 'selected' : '' }}>Productos Soda</option>
                        <option value="ticket_cat1" {{ ($filtros['tipo_producto'] ?? '') === 'ticket_cat1' ? 'selected' : '' }}>Tiquetes — Cocina</option>
                        <option value="ticket_cat2" {{ ($filtros['tipo_producto'] ?? '') === 'ticket_cat2' ? 'selected' : '' }}>Tiquetes — Externo</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('ventas', ['accion' => 'reporte']) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Tarjetas resumen --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card custom-card bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-receipt" style="font-size:2rem;opacity:.8;"></i>
                <div>
                    <div class="fs-12 opacity-75 text-uppercase fw-semibold">Transacciones</div>
                    <div class="fs-24 fw-bold">{{ $resumen['total_ventas'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card custom-card bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cash-stack" style="font-size:2rem;opacity:.8;"></i>
                <div>
                    <div class="fs-12 opacity-75 text-uppercase fw-semibold">Total Vendido</div>
                    <div class="fs-20 fw-bold">₡{{ number_format($resumen['total_monto'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card custom-card border">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-box-seam text-primary" style="font-size:2rem;"></i>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold">Ítems vendidos</div>
                    <div class="fs-22 fw-bold text-dark">{{ $resumen['total_items'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card custom-card border">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-graph-up text-success" style="font-size:2rem;"></i>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold">Promedio por venta</div>
                    <div class="fw-bold fs-18 text-dark">₡{{ number_format($resumen['promedio'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="bi bi-table me-2"></i>Detalle de Ventas
        </div>
        <a href="{{ route('ventas', array_merge(['accion' => 'reporte-pdf'], array_filter($filtros))) }}"
           target="_blank"
           class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i>Generar PDF
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaReporte" class="table table-hover align-middle w-100">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                    <tr>
                        <td class="text-muted small">{{ $venta->id }}</td>
                        <td class="fw-semibold">
                            {{ \Carbon\Carbon::parse($venta->fecha)->translatedFormat('d \d\e F Y') }}
                        </td>
                        <td>{{ $venta->user?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $venta->detalles->count() }}</span>
                        </td>
                        <td class="text-end fw-bold text-success fs-15">
                            ₡{{ number_format($venta->total_calculado, 2) }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('ventas', ['accion' => 'ver', 'id' => $venta->id]) }}"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;"></i>
                            No hay ventas para el período seleccionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($resumen['total_ventas'] > 0)
                <tfoot>
                    <tr class="table-success fw-bold">
                        <td colspan="3">Total general</td>
                        <td class="text-center">{{ $resumen['total_items'] }}</td>
                        <td class="text-end">₡{{ number_format($resumen['total_monto'], 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#tablaReporte').DataTable({
        order: [[1, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [5] }],
    });
});
</script>
@endsection
