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
        <form method="GET" action="{{ route('caja', ['accion' => 'reporte']) }}" id="formFiltros">
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

                @if(auth()->user()->rol === 1)
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
                @endif

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('caja', ['accion' => 'reporte']) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
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
                <i class="bi bi-calendar-check" style="font-size:2rem;opacity:.8;"></i>
                <div>
                    <div class="fs-12 opacity-75 text-uppercase fw-semibold">Registros</div>
                    <div class="fs-24 fw-bold">{{ $totalRegistros }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card custom-card bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cash-stack" style="font-size:2rem;opacity:.8;"></i>
                <div>
                    <div class="fs-12 opacity-75 text-uppercase fw-semibold">Total General</div>
                    <div class="fs-20 fw-bold">₡{{ number_format($totalMonto, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card custom-card border">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-calendar-range text-primary" style="font-size:2rem;"></i>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold">Período</div>
                    <div class="fw-semibold small">
                        {{ \Carbon\Carbon::parse($filtros['fecha_ini'])->format('d/m/Y') }}
                        — {{ \Carbon\Carbon::parse($filtros['fecha_fin'])->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card custom-card border">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-person-check text-secondary" style="font-size:2rem;"></i>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold">Promedio diario</div>
                    <div class="fw-semibold">
                        @if($totalRegistros > 0)
                            ₡{{ number_format($totalMonto / $totalRegistros, 2) }}
                        @else
                            ₡0.00
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla principal --}}
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="bi bi-table me-2"></i>Detalle de Cajas
        </div>
        <a href="{{ route('caja', array_merge(['accion' => 'reporte-pdf'], array_filter($filtros))) }}"
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
                        <th>Fecha</th>
                        @if(auth()->user()->rol === 1)
                        <th>Usuario</th>
                        @endif
                        <th class="text-center">Apertura</th>
                        <th class="text-center">Entrega</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Monto</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cajas as $caja)
                    <tr>
                        <td class="fw-semibold">
                            {{ \Carbon\Carbon::parse($caja->fecha)->translatedFormat('d \d\e F Y') }}
                        </td>
                        @if(auth()->user()->rol === 1)
                        <td>{{ $caja->user?->name ?? '—' }}</td>
                        @endif
                        <td class="text-center small">{{ $caja->hora_apertura?->format('H:i') ?? '—' }}</td>
                        <td class="text-center small">{{ $caja->cerrada ? ($caja->hora_cierre?->format('H:i') ?? '—') : '—' }}</td>
                        <td class="text-center">
                            @if($caja->cerrada)
                                <span class="badge bg-secondary">Entregada</span>
                            @else
                                <span class="badge bg-success">Abierta</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-success fs-15">
                            ₡{{ number_format($caja->monto, 2) }}
                        </td>
                        <td class="text-muted small">{{ $caja->observacion ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->rol === 1 ? 7 : 6 }}" class="text-center text-muted py-5">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;"></i>
                            No hay registros para el período seleccionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($totalRegistros > 0)
                <tfoot>
                    <tr class="table-success fw-bold">
                        <td colspan="{{ auth()->user()->rol === 1 ? 5 : 4 }}">Total general</td>
                        <td class="text-end">₡{{ number_format($totalMonto, 2) }}</td>
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
        order: [[0, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: -1 }],
    });
});
</script>
@endsection
