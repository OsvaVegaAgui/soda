@extends('layouts.master')

@section('styles')
<style>
    #tablaResumen thead { background: #6f42c1; color: #fff; }
    .fecha-input { max-width: 220px; }
</style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-10 mx-auto">
        <div class="card custom-card">

            <div class="card-header justify-content-between">
                <div class="card-title">
                    <i class="bi bi-bar-chart-line me-2"></i>Resumen del Día
                </div>
                <a href="{{ route('ventas', ['accion' => 'lista']) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-list-ul me-1"></i>Historial de Ventas
                </a>
            </div>

            <div class="card-body">

                {{-- Filtro de fecha --}}
                <form method="GET" action="{{ route('ventas', ['accion' => 'registrar']) }}" class="d-flex align-items-end gap-3 mb-4">
                    <div>
                        <label class="form-label fw-semibold text-muted small text-uppercase mb-1">
                            <i class="bi bi-calendar3 me-1"></i>Fecha
                        </label>
                        <input type="date" name="fecha" value="{{ $fecha }}"
                               class="form-control fecha-input" max="{{ now()->toDateString() }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Consultar
                    </button>
                    @if($fecha !== now()->toDateString())
                    <a href="{{ route('ventas', ['accion' => 'registrar']) }}" class="btn btn-outline-secondary">
                        Hoy
                    </a>
                    @endif
                </form>

                {{-- Subtítulo con la fecha seleccionada --}}
                <p class="text-muted mb-3">
                    <i class="bi bi-calendar-check me-1"></i>
                    Mostrando ventas del <strong>{{ \Carbon\Carbon::parse($fecha)->translatedFormat('l d \d\e F Y') }}</strong>
                </p>

                {{-- Tabla de ítems --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaResumen">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="width:180px;">Código</th>
                                <th class="text-center" style="width:130px;">Cant. Vendida</th>
                                <th class="text-end" style="width:150px;">Total ₡</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->nombre ?: 'Sin nombre' }}</td>
                                <td class="text-muted">{{ $item->codigo ?: '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-transparent fs-6">{{ $item->total_cantidad }}</span>
                                </td>
                                <td class="text-end fw-bold">₡{{ number_format($item->total_monto, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;"></i>
                                    No hay ventas registradas para esta fecha.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($items->isNotEmpty())
                        <tfoot>
                            <tr class="fw-bold table-light">
                                <td colspan="2">Total del día</td>
                                <td class="text-center">{{ $items->sum('total_cantidad') }}</td>
                                <td class="text-end">₡{{ number_format($totalGeneral, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
