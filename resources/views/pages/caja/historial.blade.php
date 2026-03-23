@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .saldo-positivo { color: #198754; font-weight: 700; }
</style>
@endsection

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    <i class="bi bi-clock-history me-2"></i>
                    {{ $user->rol === 1 ? 'Historial de Cajas — Todos los usuarios' : 'Mi Historial de Caja' }}
                </div>
                @if(auth()->user()->rol === 2)
                <a href="{{ route('caja', ['accion' => 'ingresar']) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-cash-coin me-1"></i>Gestionar Caja
                </a>
                @endif
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaCajas" class="table table-hover align-middle w-100">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                @if($user->rol === 1)
                                <th>Usuario</th>
                                @endif
                                <th class="text-center">Apertura</th>
                                <th class="text-center">Entrega</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Monto Inicial</th>
                                <th class="text-end">Entradas Efectivo</th>
                                <th class="text-end">Vueltos Dados</th>
                                <th class="text-end">Saldo Final</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cajas as $caja)
                            @php
                                $stats      = $ventasStats[$caja->id] ?? null;
                                $entradas   = $stats ? (float) $stats['total_efectivo'] : 0;
                                $vueltos    = $stats ? (float) $stats['total_vuelto']   : 0;
                                $saldoFinal = $caja->monto + $entradas - $vueltos;
                            @endphp
                            <tr>
                                <td class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($caja->fecha)->translatedFormat('d/m/Y') }}
                                </td>
                                @if($user->rol === 1)
                                <td>{{ $caja->user?->name ?? '—' }}</td>
                                @endif
                                <td class="text-center small">
                                    {{ $caja->hora_apertura?->format('H:i') ?? '—' }}
                                </td>
                                <td class="text-center small">
                                    {{ $caja->cerrada ? ($caja->hora_cierre?->format('H:i') ?? '—') : '—' }}
                                </td>
                                <td class="text-center">
                                    @if($caja->cerrada)
                                        <span class="badge bg-secondary">Entregada</span>
                                    @else
                                        <span class="badge bg-success">Abierta</span>
                                    @endif
                                </td>
                                <td class="text-end text-muted">
                                    ₡{{ number_format($caja->monto, 2) }}
                                </td>
                                <td class="text-end text-success fw-semibold">
                                    @if($entradas > 0)
                                        ₡{{ number_format($entradas, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end text-danger fw-semibold">
                                    @if($vueltos > 0)
                                        ₡{{ number_format($vueltos, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end saldo-positivo" style="font-size:1.05rem;">
                                    ₡{{ number_format($saldoFinal, 2) }}
                                </td>
                                <td class="text-muted small">{{ $caja->observacion ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $user->rol === 1 ? 10 : 9 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;"></i>
                                    No hay cajas registradas aún.
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
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#tablaCajas').DataTable({
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
        },
        columnDefs: [{ orderable: false, targets: -1 }],
    });
});
</script>
@endsection
