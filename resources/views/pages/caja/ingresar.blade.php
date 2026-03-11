@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-xl-6 col-lg-8 mx-auto">

        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    <i class="bi bi-cash-coin me-2"></i>Caja del Día
                    <span class="text-muted fs-6 fw-normal ms-2">
                        {{ \Carbon\Carbon::parse($hoy)->translatedFormat('l d \d\e F Y') }}
                    </span>
                </div>
                <a href="{{ route('caja', ['accion' => 'historial']) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-clock-history me-1"></i>Historial
                </a>
            </div>

            <div class="card-body">

                @if($cajaHoy)
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>
                        Ya registró la caja de hoy:
                        <strong>₡{{ number_format($cajaHoy->monto, 2) }}</strong>
                        &mdash; puede actualizar el monto a continuación.
                    </span>
                </div>
                @endif

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
                                value="{{ $cajaHoy?->monto ?? '' }}"
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
                            placeholder="Anote cualquier detalle sobre el monto ingresado...">{{ $cajaHoy?->observacion ?? '' }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="btnGuardar">
                            <i class="bi bi-save me-1"></i>
                            {{ $cajaHoy ? 'Actualizar Caja' : 'Guardar Caja' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const URL_GUARDAR = '{{ route("caja", ["accion" => "ingresar"]) }}';
    const CSRF        = '{{ csrf_token() }}';

    const form       = document.getElementById('formCaja');
    const btnGuardar = document.getElementById('btnGuardar');
    const msgBox     = document.getElementById('mensaje');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const monto       = document.getElementById('monto').value.trim();
        const observacion = document.getElementById('observacion').value.trim();

        ocultarMensaje();
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

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
                mostrarMensaje('Caja guardada correctamente: ₡' + data.monto, 'success');
                btnGuardar.innerHTML = '<i class="bi bi-check-lg me-1"></i>Actualizar Caja';
            } else {
                mostrarMensaje(data.message || 'Error al guardar.', 'danger');
                btnGuardar.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Caja';
            }
        } catch (err) {
            mostrarMensaje('Error de conexión.', 'danger');
            btnGuardar.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Caja';
        } finally {
            btnGuardar.disabled = false;
        }
    });

    function mostrarMensaje(texto, tipo) {
        msgBox.textContent = texto;
        msgBox.className   = `alert alert-${tipo}`;
    }

    function ocultarMensaje() {
        msgBox.className = 'alert d-none';
    }
});
</script>
@endsection
