@extends('layouts.master')

@section('styles')
    <style>
        .ticket-row:hover { background-color: #f3eaff; }
        .qty-input { width: 90px; text-align: center; }
        .total-badge { font-size: 1rem; }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-10 mx-auto">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Generar Tiquetes del Día</div>
                <a href="{{ route('generar-ticketes', ['accion' => 'historial']) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-clock-history me-1"></i> Ver Historial
                </a>
            </div>

            <div class="card-body">

                @if ($tickets->isEmpty())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No hay tiquetes registrados en la categoría cocina. Cree tiquetes primero.
                    </div>
                @else

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('generar-ticketes', ['accion' => 'generar']) }}"
                          id="formGenerar">
                        @csrf
                        <input type="hidden" name="download_token" id="downloadToken">

                        {{-- Fecha --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar3 me-1"></i> Fecha de los tiquetes
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha" class="form-control"
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="alert alert-info mb-0 py-2 px-3 w-100" style="font-size:0.85rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Ingrese la cantidad de tiquetes a generar por tipo. Deje en <strong>0</strong> los que no desea imprimir.
                                </div>
                            </div>
                        </div>

                        {{-- Tabla de tiquetes --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-4">
                                <thead style="background-color:#6f42c1; color:#fff;">
                                    <tr>
                                        <th>Tiquete</th>
                                        <th>Categoría</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-center">Cantidad a generar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr class="ticket-row">
                                            <td>
                                                <div class="fw-semibold">{{ $ticket->nombre }}</div>
                                                <small class="text-muted font-monospace">{{ $ticket->codigo }}</small>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color:#6f42c1">
                                                    {{ $ticket->categoria->nombre ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-end">₡{{ number_format($ticket->precio, 2) }}</td>
                                            <td class="text-center">
                                                <input type="number"
                                                       name="cantidades[{{ $ticket->id_ticket }}]"
                                                       class="form-control qty-input mx-auto cantidad-input"
                                                       value="{{ old('cantidades.' . $ticket->id_ticket, 0) }}"
                                                       min="0" max="500">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-end fw-semibold">Total de tiquetes a generar:</td>
                                        <td class="text-center">
                                            <span id="totalTiquetes" class="badge bg-primary total-badge">0</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGenerar">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnLimpiar">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                            </button>
                        </div>

                    </form>

                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const inputs   = document.querySelectorAll('.cantidad-input');
    const totalEl  = document.getElementById('totalTiquetes');
    const btnGen   = document.getElementById('btnGenerar');
    const btnLimp  = document.getElementById('btnLimpiar');

    function recalcTotal() {
        let total = 0;
        inputs.forEach(inp => { total += Math.max(0, parseInt(inp.value) || 0); });
        totalEl.textContent = total;
        totalEl.className = total > 0
            ? 'badge bg-primary total-badge'
            : 'badge bg-secondary total-badge';
    }

    inputs.forEach(inp => inp.addEventListener('input', recalcTotal));
    recalcTotal();

    // Limpiar todas las cantidades
    if (btnLimp) {
        btnLimp.addEventListener('click', () => {
            inputs.forEach(inp => { inp.value = 0; });
            recalcTotal();
        });
    }

    // Indicar que está generando y restaurar cuando el download termina (cookie polling)
    if (btnGen) {
        document.getElementById('formGenerar').addEventListener('submit', () => {
            const token = Date.now().toString();
            document.getElementById('downloadToken').value = token;

            btnGen.disabled = true;
            btnGen.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generando PDF...';

            const poller = setInterval(() => {
                if (document.cookie.split(';').some(c => c.trim() === 'pdf_ready=' + token)) {
                    document.cookie = 'pdf_ready=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
                    clearInterval(poller);
                    btnGen.disabled = false;
                    btnGen.innerHTML = '<i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF';
                }
            }, 500);
        });
    }

});
</script>
@endsection
