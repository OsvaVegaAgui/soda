@extends('layouts.master')

@section('styles')
<style>
    .peak-badge {
        background: linear-gradient(135deg, #6f42c1, #a679f0);
        color: #fff;
        border-radius: .5rem;
        padding: .15rem .55rem;
        font-size: .75rem;
        font-weight: 600;
    }
    .hora-activa { background-color: rgba(111,66,193,.08); }
</style>
@endsection

@section('content')

{{-- Filtros --}}
<div class="card custom-card mb-3">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-funnel me-2"></i>Filtros</div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('ventas', ['accion' => 'reporte-horas']) }}">
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

                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('ventas', ['accion' => 'reporte-horas']) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Tarjetas resumen --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-4">
        <div class="card custom-card bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cash-stack" style="font-size:2rem;opacity:.8;"></i>
                <div>
                    <div class="fs-12 opacity-75 text-uppercase fw-semibold">Total vendido</div>
                    <div class="fs-20 fw-bold">₡{{ number_format($totalMonto, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card custom-card bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-receipt" style="font-size:2rem;opacity:.8;"></i>
                <div>
                    <div class="fs-12 opacity-75 text-uppercase fw-semibold">Transacciones</div>
                    <div class="fs-24 fw-bold">{{ $totalTransacciones }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-xl-4">
        <div class="card custom-card border">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-clock-fill text-warning" style="font-size:2rem;"></i>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold">Hora pico</div>
                    @if($peakHora['monto'] > 0)
                    <div class="fw-bold fs-20 text-dark">
                        {{ $peakHora['hora'] }}
                        <span class="peak-badge ms-1">₡{{ number_format($peakHora['monto'], 0) }}</span>
                    </div>
                    <div class="fs-12 text-muted">{{ $peakHora['transacciones'] }} transacción(es)</div>
                    @else
                    <div class="text-muted fs-14">Sin datos</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gráfico --}}
<div class="card custom-card mb-3">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-bar-chart-line me-2"></i>Ventas por hora del día
        </div>
        <small class="text-muted ms-auto">
            {{ \Carbon\Carbon::parse($filtros['fecha_ini'])->translatedFormat('d M Y') }}
            — {{ \Carbon\Carbon::parse($filtros['fecha_fin'])->translatedFormat('d M Y') }}
        </small>
    </div>
    <div class="card-body">
        <canvas id="chartHoras" style="min-height:340px;"></canvas>
    </div>
</div>

{{-- Tabla detalle --}}
<div class="card custom-card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-table me-2"></i>Detalle por hora</div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Hora</th>
                        <th class="text-center">Transacciones</th>
                        <th class="text-end">Monto total</th>
                        <th class="text-end">Promedio / transacción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($horas as $h)
                    @if($h['monto'] > 0)
                    <tr class="{{ $h['hora'] === $peakHora['hora'] && $peakHora['monto'] > 0 ? 'hora-activa' : '' }}">
                        <td class="fw-semibold">
                            {{ $h['hora'] }}
                            @if($h['hora'] === $peakHora['hora'] && $peakHora['monto'] > 0)
                                <span class="peak-badge ms-1">pico</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $h['transacciones'] }}</span>
                        </td>
                        <td class="text-end fw-bold text-success">
                            ₡{{ number_format($h['monto'], 2) }}
                        </td>
                        <td class="text-end text-muted">
                            ₡{{ $h['transacciones'] > 0 ? number_format($h['monto'] / $h['transacciones'], 2) : '—' }}
                        </td>
                    </tr>
                    @endif
                    @endforeach

                    @if($totalMonto == 0)
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;"></i>
                            No hay ventas para el período seleccionado.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels  = @json(array_column($horas, 'hora'));
    const montos  = @json(array_column($horas, 'monto'));
    const ventas  = @json(array_column($horas, 'transacciones'));

    const ctx = document.getElementById('chartHoras').getContext('2d');

    new Chart(ctx, {
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Monto vendido (₡)',
                    data: montos,
                    backgroundColor: 'rgba(111, 66, 193, 0.75)',
                    borderColor:     'rgba(111, 66, 193, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'yMonto',
                    order: 2,
                },
                {
                    type: 'line',
                    label: 'Transacciones',
                    data: ventas,
                    borderColor:     'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.15)',
                    borderWidth: 2.5,
                    pointBackgroundColor: 'rgba(255, 159, 64, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'yVentas',
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (ctx.dataset.yAxisID === 'yMonto') {
                                return ' ₡' + ctx.parsed.y.toLocaleString('es-CR', { minimumFractionDigits: 2 });
                            }
                            return ' ' + ctx.parsed.y + ' transacción(es)';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                yMonto: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: val => '₡' + val.toLocaleString('es-CR'),
                        font: { size: 11 }
                    },
                    grid: { color: 'rgba(0,0,0,.06)' }
                },
                yVentas: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 11 }
                    },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
})();
</script>
@endsection
