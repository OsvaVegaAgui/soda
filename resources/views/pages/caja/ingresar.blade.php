@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-xl-6 col-lg-8 mx-auto">

        {{-- Cajas cerradas hoy --}}
        @if($cajasCerradasHoy->count() > 0)
        <div class="card custom-card mb-3">
            <div class="card-header">
                <div class="card-title">
                    <i class="bi bi-archive me-2 text-secondary"></i>Cajas entregadas hoy
                </div>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($cajasCerradasHoy as $c)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-secondary me-2">#{{ $loop->iteration }}</span>
                        <strong>₡{{ number_format($c->monto, 2) }}</strong>
                        @if($c->observacion)
                            <span class="text-muted ms-2 small">— {{ $c->observacion }}</span>
                        @endif
                    </div>
                    <div class="text-end text-muted small">
                        <div>Apertura: {{ $c->hora_apertura?->format('H:i') ?? '—' }}</div>
                        <div>Entrega: {{ $c->hora_cierre?->format('H:i') ?? '—' }}</div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Caja activa --}}
        @if($cajaActiva)
        <div class="card custom-card border-success">
            <div class="card-header justify-content-between bg-success bg-opacity-10">
                <div class="card-title text-success">
                    <i class="bi bi-cash-coin me-2"></i>Caja Activa
                    <span class="text-muted fs-6 fw-normal ms-2">
                        {{ \Carbon\Carbon::parse($hoy)->translatedFormat('l d \d\e F Y') }}
                    </span>
                </div>
                <span class="badge bg-success fs-12">
                    <i class="bi bi-circle-fill me-1" style="font-size:.5rem;"></i>Abierta
                </span>
            </div>
            <div class="card-body">

                <div id="mensajeCerrar" class="alert d-none mb-3" role="alert"></div>

                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="p-3 rounded bg-light text-center">
                            <div class="text-muted small mb-1">Monto inicial</div>
                            <div class="fs-5 fw-bold text-muted">₡{{ number_format($cajaActiva->monto, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded bg-light text-center">
                            <div class="text-muted small mb-1">Saldo actual</div>
                            <div class="fs-5 fw-bold text-success">₡{{ number_format($saldoActiva, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded bg-light text-center">
                            <div class="text-muted small mb-1">Hora apertura</div>
                            <div class="fs-5 fw-bold">{{ $cajaActiva->hora_apertura?->format('H:i') ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                @if($cajaActiva->observacion)
                <div class="alert alert-light border mb-4">
                    <i class="bi bi-chat-left-text me-2 text-muted"></i>{{ $cajaActiva->observacion }}
                </div>
                @endif

                <div class="d-grid">
                    <button type="button" class="btn btn-warning btn-lg" id="btnCerrar"
                            data-caja-id="{{ $cajaActiva->id }}"
                            data-bs-toggle="modal" data-bs-target="#modalConfirmarEntrega">
                        <i class="bi bi-box-arrow-in-down-right me-1"></i>Entregar Caja
                    </button>
                </div>

            </div>
        </div>

        {{-- Modal confirmación entrega --}}
        <div class="modal fade" id="modalConfirmarEntrega" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">
                            <i class="bi bi-box-arrow-in-down-right me-2 text-warning"></i>Entregar Caja
                        </h5>
                    </div>
                    <div class="modal-body pt-2">
                        <p class="mb-1">¿Confirma que desea entregar la caja actual?</p>
                        <p class="text-muted small mb-0">
                            Saldo actual:
                            <strong class="text-success">₡{{ number_format($saldoActiva, 2) }}</strong>
                            &nbsp;·&nbsp; Apertura: <strong>{{ $cajaActiva->hora_apertura?->format('H:i') ?? '—' }}</strong>
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelarEntrega">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" id="btnConfirmarEntrega">
                            <i class="bi bi-box-arrow-in-down-right me-1"></i>Sí, entregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @else
        {{-- Formulario nueva caja --}}
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    <i class="bi bi-cash-coin me-2"></i>
                    @if($cajasCerradasHoy->count() > 0)
                        Nueva Caja
                    @else
                        Caja del Día
                    @endif
                    <span class="text-muted fs-6 fw-normal ms-2">
                        {{ \Carbon\Carbon::parse($hoy)->translatedFormat('l d \d\e F Y') }}
                    </span>
                </div>
                <a href="{{ route('caja', ['accion' => 'historial']) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-clock-history me-1"></i>Historial
                </a>
            </div>

            <div class="card-body">

                <div id="mensaje" class="alert d-none mb-3" role="alert"></div>

                <form id="formCaja" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="monto" class="form-label fw-semibold">
                            Monto de caja <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₡</span>
                            <input
                                type="number"
                                id="monto"
                                name="monto"
                                class="form-control form-control-lg"
                                placeholder="0.00"
                                min="0"
                                step="0.01"
                                autofocus
                                required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacion" class="form-label fw-semibold">Observación <span class="text-muted fw-normal">(opcional)</span></label>
                        <textarea
                            id="observacion"
                            name="observacion"
                            class="form-control"
                            rows="3"
                            maxlength="500"
                            placeholder="Anote cualquier detalle sobre el monto ingresado..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="btnGuardar">
                            <i class="bi bi-cash-coin me-1"></i>Abrir Caja
                        </button>
                    </div>
                </form>

            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const CSRF = '{{ csrf_token() }}';

    // ── Abrir nueva caja ───────────────────────────────────────────────────
    const formCaja = document.getElementById('formCaja');
    if (formCaja) {
        const URL_GUARDAR = '{{ route("caja", ["accion" => "ingresar"]) }}';
        const btnGuardar  = document.getElementById('btnGuardar');
        const msgBox      = document.getElementById('mensaje');

        formCaja.addEventListener('submit', async (e) => {
            e.preventDefault();

            const monto       = document.getElementById('monto').value.trim();
            const observacion = document.getElementById('observacion').value.trim();

            ocultarMensaje(msgBox);
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Abriendo...';

            try {
                const res  = await fetch(URL_GUARDAR, {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'X-CSRF-TOKEN':     CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ monto, observacion }),
                });

                const data = await res.json();

                if (data.ok) {
                    mostrarMensaje(msgBox, 'Caja abierta: ₡' + data.monto + '. Recargando...', 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    mostrarMensaje(msgBox, data.message || 'Error al guardar.', 'danger');
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="bi bi-cash-coin me-1"></i>Abrir Caja';
                }
            } catch (err) {
                mostrarMensaje(msgBox, 'Error de conexión.', 'danger');
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="bi bi-cash-coin me-1"></i>Abrir Caja';
            }
        });
    }

    // ── Entregar / cerrar caja ─────────────────────────────────────────────
    const btnConfirmarEntrega = document.getElementById('btnConfirmarEntrega');
    if (btnConfirmarEntrega) {
        const btnCerrar    = document.getElementById('btnCerrar');
        const msgBoxCerrar = document.getElementById('mensajeCerrar');
        const modal        = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEntrega'))
                             ?? new bootstrap.Modal(document.getElementById('modalConfirmarEntrega'));

        btnConfirmarEntrega.addEventListener('click', async () => {
            const cajaId     = btnCerrar.dataset.cajaId;
            const URL_CERRAR = `/caja/cerrar/${cajaId}`;

            btnConfirmarEntrega.disabled = true;
            btnConfirmarEntrega.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Entregando...';
            document.getElementById('btnCancelarEntrega').disabled = true;

            try {
                const res  = await fetch(URL_CERRAR, {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'X-CSRF-TOKEN':     CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await res.json();

                if (data.ok) {
                    modal.hide();
                    mostrarMensaje(msgBoxCerrar, 'Caja entregada correctamente. Recargando...', 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    modal.hide();
                    mostrarMensaje(msgBoxCerrar, data.message || 'Error al entregar.', 'danger');
                    btnConfirmarEntrega.disabled = false;
                    btnConfirmarEntrega.innerHTML = '<i class="bi bi-box-arrow-in-down-right me-1"></i>Sí, entregar';
                    document.getElementById('btnCancelarEntrega').disabled = false;
                }
            } catch (err) {
                modal.hide();
                mostrarMensaje(msgBoxCerrar, 'Error de conexión.', 'danger');
                btnConfirmarEntrega.disabled = false;
                btnConfirmarEntrega.innerHTML = '<i class="bi bi-box-arrow-in-down-right me-1"></i>Sí, entregar';
                document.getElementById('btnCancelarEntrega').disabled = false;
            }
        });
    }

    function mostrarMensaje(box, texto, tipo) {
        box.textContent = texto;
        box.className   = `alert alert-${tipo}`;
    }

    function ocultarMensaje(box) {
        box.className = 'alert d-none';
    }
});
</script>
@endsection
