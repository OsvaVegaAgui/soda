@extends('layouts.master')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    /* ── Total display ─────────────────────────────────────────────────── */
    .total-panel {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
        border-radius: 1rem;
        padding: 1.5rem 2rem;
        box-shadow: 0 8px 32px rgba(0,0,0,.35);
        text-align: center;
    }
    .total-label {
        font-size: .85rem;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: rgba(255,255,255,.5);
        margin-bottom: .25rem;
    }
    .total-amount {
        font-size: clamp(2.8rem, 6vw, 5rem);
        font-weight: 900;
        color: #fff;
        line-height: 1;
        letter-spacing: -1px;
        font-variant-numeric: tabular-nums;
        transition: color .2s;
    }
    .total-amount.updated {
        color: #2ecc71;
    }
    .total-items {
        font-size: .95rem;
        color: rgba(255,255,255,.45);
        margin-top: .4rem;
    }

    /* ── Panel de método de pago ────────────────────────────────────────── */
    .pago-panel {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,.35);
    }
    .pago-titulo {
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: rgba(255,255,255,.5);
        margin-bottom: .75rem;
    }
    .pago-opciones {
        display: flex;
        gap: .75rem;
    }
    .pago-opcion {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .3rem;
        padding: .75rem .5rem;
        border-radius: .6rem;
        border: 1.5px solid rgba(255,255,255,.15);
        color: rgba(255,255,255,.5);
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
        transition: border-color .2s, background .2s, color .2s;
        text-align: center;
    }
    .pago-opcion i { font-size: 1.5rem; }
    .pago-opcion input[type=checkbox] { display: none; }
    .pago-opcion.activo {
        border-color: #2ecc71;
        background: rgba(46,204,113,.15);
        color: #2ecc71;
    }
    .pago-opcion:hover:not(.activo) {
        border-color: rgba(255,255,255,.35);
        color: rgba(255,255,255,.8);
    }

    /* ── Sección efectivo ───────────────────────────────────────────────── */
    #seccionEfectivo {
        margin-top: .85rem;
        display: none;
    }
    .efectivo-label {
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,.45);
        margin-bottom: .4rem;
    }
    #montoEfectivo {
        background: rgba(255,255,255,.07);
        border: 1.5px solid rgba(255,255,255,.2);
        color: #fff;
        font-size: 1.2rem;
        font-weight: 700;
        border-radius: .5rem;
        padding: .5rem .75rem;
        width: 100%;
        outline: none;
        transition: border-color .2s;
    }
    #montoEfectivo::placeholder { color: rgba(255,255,255,.25); }
    #montoEfectivo:focus { border-color: #2ecc71; }
    #montoEfectivo::-webkit-outer-spin-button,
    #montoEfectivo::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .vuelto-box {
        margin-top: .6rem;
        background: rgba(46,204,113,.12);
        border: 1px solid rgba(46,204,113,.3);
        border-radius: .5rem;
        padding: .5rem .85rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .vuelto-label-txt {
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,.45);
    }
    .vuelto-valor {
        font-size: 1.3rem;
        font-weight: 900;
        color: #2ecc71;
        font-variant-numeric: tabular-nums;
    }
    .vuelto-insuficiente { border-color: rgba(231,76,60,.4); background: rgba(231,76,60,.1); }
    .vuelto-insuficiente .vuelto-valor { color: #e74c3c; }

    /* ── Botones de acción ─────────────────────────────────────────────── */
    .btn-procesar {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        border: none;
        color: #fff;
        font-size: 1.15rem;
        font-weight: 700;
        padding: .85rem 1.5rem;
        border-radius: .6rem;
        letter-spacing: .5px;
        box-shadow: 0 4px 15px rgba(0,176,155,.4);
        transition: transform .15s, box-shadow .15s, opacity .15s;
    }
    .btn-procesar:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,176,155,.55);
        color: #fff;
    }
    .btn-procesar:disabled { opacity: .55; cursor: not-allowed; }

    .btn-nueva {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.2);
        color: rgba(255,255,255,.75);
        font-size: .9rem;
        font-weight: 600;
        padding: .55rem 1rem;
        border-radius: .5rem;
        transition: background .15s, color .15s;
    }
    .btn-nueva:hover { background: rgba(255,255,255,.18); color: #fff; }

    /* ── Tabla de ítems ────────────────────────────────────────────────── */
    #tablaPos thead { background: #6f42c1; color: #fff; }
    .btn-qty {
        width: 30px; height: 30px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; font-size: 1rem; line-height: 1;
    }
    .qty-val { font-size: 1.1rem; font-weight: 700; min-width: 2rem; display: inline-block; text-align: center; }

    /* ── Scanner ───────────────────────────────────────────────────────── */
    #scannerInput:focus { border-color: #6f42c1; box-shadow: 0 0 0 .2rem rgba(111,66,193,.2); }

    /* ── Select2 ───────────────────────────────────────────────────────── */
    .select2-container .select2-selection--single {
        height: 42px; border: 1px solid #dee2e6; border-radius: .375rem;
        display: flex; align-items: center; padding: 0 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: normal; padding: 0; color: #212529; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { top: 50%; transform: translateY(-50%); }
    .select2-dropdown { border-color: #dee2e6; border-radius: .375rem; }
    .select2-container--default .select2-results__option--highlighted { background-color: #6f42c1; }

    /* ── Toast ─────────────────────────────────────────────────────────── */
    .toast-stack { position: fixed; bottom: 1.5rem; right: 1.5rem; display: flex; flex-direction: column; gap: .5rem; z-index: 9999; pointer-events: none; }
    .toast-item { padding: .55rem 1rem; border-radius: .4rem; font-size: .9rem; font-weight: 500; opacity: 0; transition: opacity .25s; pointer-events: none; }
    .toast-item.show { opacity: 1; }
    .toast-success { background: #198754; color: #fff; }
    .toast-danger  { background: #dc3545; color: #fff; }

    /* ── Modal éxito ───────────────────────────────────────────────────── */
    .modal-exito .modal-content { border-radius: 1rem; border: none; overflow: hidden; }
    .modal-exito .exito-total { font-size: 3.5rem; font-weight: 900; color: #198754; }
    .modal-exito .exito-vuelto-box {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        border-radius: .6rem;
        padding: .6rem 1rem;
        margin-bottom: 1rem;
    }
    .modal-exito .exito-vuelto-label { font-size: .75rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #065f46; }
    .modal-exito .exito-vuelto-monto { font-size: 2rem; font-weight: 900; color: #059669; }
</style>
@endsection

@section('content')

<div class="row g-3">

    {{-- Panel lateral: total + pago + acciones --}}
    <div class="col-xl-4 col-lg-5">

        {{-- Total --}}
        <div class="total-panel mb-3">
            <p class="total-label">Total a cobrar</p>
            <div class="total-amount" id="totalDisplay">
                ₡<span id="totalValor">{{ number_format($total, 2) }}</span>
            </div>
            <p class="total-items" id="totalItems">
                <span id="itemsCount">{{ count($items) }}</span> ítem(s)
            </p>
        </div>

        {{-- Método de pago --}}
        <div class="pago-panel mb-3">
            <p class="pago-titulo"><i class="bi bi-wallet2 me-1"></i>Método de pago</p>
            <div class="pago-opciones">
                <label class="pago-opcion" id="opEfectivo">
                    <input type="checkbox" id="chkEfectivo">
                    <i class="bi bi-cash-stack"></i>
                    Efectivo
                </label>
                <label class="pago-opcion" id="opTarjeta">
                    <input type="checkbox" id="chkTarjeta">
                    <i class="bi bi-credit-card-2-front"></i>
                    Tarjeta
                </label>
            </div>

            {{-- Sección efectivo (se muestra solo si chkEfectivo está activo) --}}
            <div id="seccionEfectivo">
                <p class="efectivo-label mt-1">Monto recibido en efectivo</p>
                <input type="number" id="montoEfectivo" min="0" step="1"
                       placeholder="0" autocomplete="off">
                <div class="vuelto-box mt-2" id="vueltoBox">
                    <span class="vuelto-label-txt">Vuelto</span>
                    <span class="vuelto-valor" id="vueltoValor">₡0.00</span>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="d-grid gap-2">
            <button class="btn-procesar btn" id="btnProcesar" disabled>
                <i class="bi bi-check-circle me-2"></i>Procesar Venta
            </button>
            <button class="btn-nueva btn" id="btnNueva">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Nueva Venta
            </button>
        </div>

        {{-- Enlace historial --}}
        <div class="mt-3 text-center">
            <a href="{{ route('ventas', ['accion' => 'lista']) }}" class="text-muted small">
                <i class="bi bi-clock-history me-1"></i>Ver historial de ventas
            </a>
        </div>

    </div>

    {{-- Panel principal: scanner + lista --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card custom-card h-100">

            <div class="card-header justify-content-between">
                <div class="card-title">
                    <i class="bi bi-cart3 me-2"></i>Cobro por Venta
                    <span class="text-muted fs-6 fw-normal ms-2">
                        {{ \Carbon\Carbon::now()->translatedFormat('l d \d\e F Y') }}
                    </span>
                </div>
                <a href="{{ route('ventas', ['accion' => 'registrar']) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-calendar-day me-1"></i>Control Diario
                </a>
            </div>

            <div class="card-body">

                {{-- Scanner --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small text-uppercase">
                        <i class="bi bi-upc-scan me-1"></i>Lector de código
                    </label>
                    <input type="text" id="scannerInput" class="form-control form-control-lg"
                           placeholder="Pase el lector de código aquí..."
                           autocomplete="off" spellcheck="false">
                </div>

                {{-- Búsqueda manual --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted small text-uppercase">
                        <i class="bi bi-search me-1"></i>Búsqueda manual
                    </label>
                    <select id="busquedaManual" class="form-select" style="width:100%">
                        <option></option>
                    </select>
                </div>

                {{-- Lista de ítems --}}
                <table class="table table-hover align-middle mb-0" id="tablaPos">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center" style="width:160px;">Cantidad</th>
                            <th class="text-end" style="width:130px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPos">
                        @forelse($items as $item)
                        <tr id="pos-row-{{ $item['codigo'] }}">
                            <td class="fw-semibold">{{ $item['nombre'] }}</td>
                            <td class="text-center">
                                <button class="btn btn-outline-danger btn-qty btn-quitar" data-codigo="{{ $item['codigo'] }}">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <span class="qty-val">{{ $item['cantidad'] }}</span>
                                <button class="btn btn-outline-success btn-qty btn-mas" data-codigo="{{ $item['codigo'] }}">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </td>
                            <td class="text-end fw-bold">₡{{ number_format($item['subtotal'], 2) }}</td>
                        </tr>
                        @empty
                        <tr id="rowVacio">
                            <td colspan="3" class="text-center text-muted py-5">
                                <i class="bi bi-upc-scan d-block mb-2" style="font-size:2.5rem;"></i>
                                Escanee o busque un producto para comenzar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

{{-- Modal éxito --}}
<div class="modal fade modal-exito" id="modalExito" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="mb-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
            </div>
            <h4 class="fw-bold mb-1">¡Venta procesada!</h4>
            <p class="text-muted mb-2">Total cobrado:</p>
            <div class="exito-total mb-3" id="exitoTotal">₡0.00</div>

            {{-- Vuelto (solo aparece si hubo pago en efectivo) --}}
            <div class="exito-vuelto-box" id="exitoVueltoBox" style="display:none;">
                <p class="exito-vuelto-label mb-0">Vuelto a entregar</p>
                <div class="exito-vuelto-monto" id="exitoVueltoMonto">₡0.00</div>
            </div>

            <button class="btn btn-success btn-lg w-100" id="btnNuevaDesdeModal">
                <i class="bi bi-plus-circle me-2"></i>Nueva Venta
            </button>
        </div>
    </div>
</div>

<div class="toast-stack" id="toastStack"></div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const URLS = {
        agregar:  '{{ route("ventas", ["accion" => "pos-agregar"]) }}',
        quitar:   '{{ route("ventas", ["accion" => "pos-quitar"]) }}',
        procesar: '{{ route("ventas", ["accion" => "pos-procesar"]) }}',
        limpiar:  '{{ route("ventas", ["accion" => "pos-limpiar"]) }}',
        buscar:   '{{ route("ventas", ["accion" => "buscar-producto"]) }}',
    };
    const CSRF = '{{ csrf_token() }}';

    // ── Estado de pago ────────────────────────────────────────────────────────
    let totalActual = {{ $total }};

    const chkEfectivo  = document.getElementById('chkEfectivo');
    const chkTarjeta   = document.getElementById('chkTarjeta');
    const opEfectivo   = document.getElementById('opEfectivo');
    const opTarjeta    = document.getElementById('opTarjeta');
    const secEfectivo  = document.getElementById('seccionEfectivo');
    const montoInput   = document.getElementById('montoEfectivo');
    const vueltoBox    = document.getElementById('vueltoBox');
    const vueltoValor  = document.getElementById('vueltoValor');

    function getMetodoPago() {
        const e = chkEfectivo.checked;
        const t = chkTarjeta.checked;
        if (e && t)  return 'mixto';
        if (e)       return 'efectivo';
        if (t)       return 'tarjeta';
        return null;
    }

    function calcularVuelto() {
        const monto  = parseFloat(montoInput.value) || 0;
        const vuelto = monto - totalActual;
        vueltoValor.textContent = '₡' + Math.abs(vuelto).toLocaleString('es-CR', { minimumFractionDigits: 2 });
        if (vuelto < 0) {
            vueltoBox.classList.add('vuelto-insuficiente');
        } else {
            vueltoBox.classList.remove('vuelto-insuficiente');
        }
        return vuelto;
    }

    function actualizarEstadoPago() {
        const metodo    = getMetodoPago();
        const hayItems  = !!document.querySelector('#tbodyPos tr[id^="pos-row-"]');

        // Mostrar/ocultar sección efectivo
        const conEfectivo = chkEfectivo.checked;
        secEfectivo.style.display = conEfectivo ? 'block' : 'none';
        if (!conEfectivo) montoInput.value = '';

        // Estilos de los cards-checkbox
        opEfectivo.classList.toggle('activo', chkEfectivo.checked);
        opTarjeta.classList.toggle('activo', chkTarjeta.checked);

        // Habilitar botón procesar
        let puedeProcessar = hayItems && metodo !== null;
        if (conEfectivo && metodo === 'efectivo') {
            const monto = parseFloat(montoInput.value) || 0;
            puedeProcessar = puedeProcessar && monto >= totalActual;
        }
        if (conEfectivo && metodo === 'mixto') {
            const monto = parseFloat(montoInput.value) || 0;
            puedeProcessar = puedeProcessar && monto > 0;
        }

        document.getElementById('btnProcesar').disabled = !puedeProcessar;
    }

    // Toggle checkboxes
    opEfectivo.addEventListener('click', () => {
        chkEfectivo.checked = !chkEfectivo.checked;
        actualizarEstadoPago();
    });
    opTarjeta.addEventListener('click', () => {
        chkTarjeta.checked = !chkTarjeta.checked;
        actualizarEstadoPago();
    });

    // Calcular vuelto mientras escribe
    montoInput.addEventListener('input', () => {
        calcularVuelto();
        actualizarEstadoPago();
    });

    // ── Scanner ───────────────────────────────────────────────────────────────
    const scannerInput = document.getElementById('scannerInput');
    scannerInput.focus();

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.select2-container') &&
            !e.target.closest('#busquedaManual') &&
            !e.target.closest('#montoEfectivo') &&
            !e.target.closest('.pago-opcion')) {
            scannerInput.focus();
        }
    });

    scannerInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const codigo = scannerInput.value.trim();
            if (codigo) { agregarItem(codigo); scannerInput.value = ''; }
        }
    });

    // ── Select2 ───────────────────────────────────────────────────────────────
    $('#busquedaManual').select2({
        placeholder: 'Escriba nombre o código del producto...',
        minimumInputLength: 2,
        language: {
            inputTooShort: () => 'Escriba al menos 2 caracteres...',
            searching: () => 'Buscando...',
            noResults: () => 'No se encontraron productos',
        },
        ajax: {
            url: URLS.buscar,
            dataType: 'json',
            delay: 300,
            data: (params) => ({ term: params.term }),
            processResults: (data) => {
                if (!data.success) return { results: [] };
                return { results: data.productos.map(p => ({ id: p.codigo, text: p.etiqueta, codigo: p.codigo })) };
            },
        },
    });

    $('#busquedaManual').on('select2:select', (e) => {
        agregarItem(e.params.data.codigo);
        $('#busquedaManual').val(null).trigger('change');
        setTimeout(() => scannerInput.focus(), 150);
    });

    // ── Agregar ítem ──────────────────────────────────────────────────────────
    function agregarItem(codigo) {
        fetch(URLS.agregar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ codigo }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                renderFila(data.item);
                actualizarTotal(data.total);
                toast(data.message, 'success');
            } else {
                toast(data.message, 'danger');
            }
        })
        .catch(() => toast('Error de conexión.', 'danger'));
    }

    // ── Botones +/- (delegación) ──────────────────────────────────────────────
    document.getElementById('tbodyPos').addEventListener('click', (e) => {
        const btnMas    = e.target.closest('.btn-mas');
        const btnQuitar = e.target.closest('.btn-quitar');

        if (btnMas) agregarItem(btnMas.dataset.codigo);

        if (btnQuitar) {
            fetch(URLS.quitar, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ codigo: btnQuitar.dataset.codigo }),
            })
            .then(r => r.json())
            .then(data => {
                if (!data.ok) return;
                if (data.eliminado) {
                    document.getElementById('pos-row-' + data.codigo)?.remove();
                    if (!document.querySelector('#tbodyPos tr:not(#rowVacio)')) mostrarVacio();
                    toast(data.nombre + ' eliminado.', 'danger');
                } else {
                    const row = document.getElementById('pos-row-' + data.codigo);
                    if (row) {
                        row.querySelector('.qty-val').textContent = data.cantidad;
                        const precio = parseFloat(row.dataset.precio || 0);
                        if (!precio) row.querySelector('td:last-child').textContent = '';
                    }
                    toast('Cantidad actualizada.', 'success');
                }
                actualizarTotal(data.total);
            });
        }
    });

    // ── Procesar venta ────────────────────────────────────────────────────────
    document.getElementById('btnProcesar').addEventListener('click', () => {
        const btn    = document.getElementById('btnProcesar');
        const metodo = getMetodoPago();

        if (!metodo) { toast('Seleccione un método de pago.', 'danger'); return; }

        const payload = { metodo_pago: metodo };
        if (chkEfectivo.checked) {
            payload.monto_efectivo = parseFloat(montoInput.value) || 0;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

        fetch(URLS.procesar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                document.getElementById('exitoTotal').textContent = '₡' + data.total;

                // Mostrar vuelto en modal si aplica
                const vueltoBox  = document.getElementById('exitoVueltoBox');
                const vueltoMonto = document.getElementById('exitoVueltoMonto');
                if (data.vuelto !== null && data.vuelto !== undefined) {
                    vueltoMonto.textContent = '₡' + data.vuelto;
                    vueltoBox.style.display = 'block';
                } else {
                    vueltoBox.style.display = 'none';
                }

                new bootstrap.Modal(document.getElementById('modalExito')).show();
            } else {
                toast(data.message, 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Procesar Venta';
            }
        })
        .catch(() => {
            toast('Error de conexión.', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Procesar Venta';
        });
    });

    // ── Nueva venta ───────────────────────────────────────────────────────────
    function limpiarCarrito() {
        fetch(URLS.limpiar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({}),
        }).then(() => {
            document.getElementById('tbodyPos').innerHTML = '';
            mostrarVacio();
            actualizarTotal(0);

            // Reset método de pago
            chkEfectivo.checked = false;
            chkTarjeta.checked  = false;
            montoInput.value    = '';
            opEfectivo.classList.remove('activo');
            opTarjeta.classList.remove('activo');
            secEfectivo.style.display = 'none';

            document.getElementById('btnProcesar').disabled = true;
            document.getElementById('btnProcesar').innerHTML = '<i class="bi bi-check-circle me-2"></i>Procesar Venta';
            scannerInput.value = '';
            scannerInput.focus();
        });
    }

    document.getElementById('btnNueva').addEventListener('click', () => {
        if (document.querySelector('#tbodyPos tr:not(#rowVacio)') && !confirm('¿Cancelar la venta actual e iniciar una nueva?')) return;
        limpiarCarrito();
    });

    document.getElementById('btnNuevaDesdeModal').addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('modalExito')).hide();
        limpiarCarrito();
    });

    // ── DOM helpers ───────────────────────────────────────────────────────────
    function renderFila(item) {
        document.getElementById('rowVacio')?.remove();

        const codigo   = item.codigo;
        const subtotal = (item.cantidad * item.precio_unitario).toFixed(2);

        let row = document.getElementById('pos-row-' + codigo);
        if (row) {
            row.querySelector('.qty-val').textContent = item.cantidad;
            row.querySelector('td:last-child').textContent = '₡' + Number(subtotal).toLocaleString('es-CR', { minimumFractionDigits: 2 });
            row.classList.add('table-warning');
            setTimeout(() => row.classList.remove('table-warning'), 600);
        } else {
            const tbody = document.getElementById('tbodyPos');
            const tr    = document.createElement('tr');
            tr.id = 'pos-row-' + codigo;
            tr.dataset.precio = item.precio_unitario;
            tr.innerHTML = `
                <td class="fw-semibold">${escHtml(item.nombre)}</td>
                <td class="text-center">
                    <button class="btn btn-outline-danger btn-qty btn-quitar" data-codigo="${escHtml(codigo)}">
                        <i class="bi bi-dash"></i>
                    </button>
                    <span class="qty-val">${item.cantidad}</span>
                    <button class="btn btn-outline-success btn-qty btn-mas" data-codigo="${escHtml(codigo)}">
                        <i class="bi bi-plus"></i>
                    </button>
                </td>
                <td class="text-end fw-bold">₡${Number(subtotal).toLocaleString('es-CR', { minimumFractionDigits: 2 })}</td>`;
            const filas = [...tbody.querySelectorAll('tr[id^="pos-row-"]')];
            const sig   = filas.find(f => f.querySelector('td')?.textContent.trim().toLowerCase() > item.nombre.toLowerCase());
            sig ? tbody.insertBefore(tr, sig) : tbody.appendChild(tr);
        }

        actualizarEstadoPago(); // re-evaluar botón al agregar ítem
    }

    function actualizarTotal(valor) {
        totalActual = parseFloat(valor) || 0;
        const el = document.getElementById('totalValor');
        el.textContent = totalActual.toLocaleString('es-CR', { minimumFractionDigits: 2 });

        const totalEl = document.getElementById('totalDisplay');
        totalEl.classList.add('updated');
        setTimeout(() => totalEl.classList.remove('updated'), 600);

        const count = document.querySelectorAll('#tbodyPos tr[id^="pos-row-"]').length;
        document.getElementById('itemsCount').textContent = count;

        // Recalcular vuelto si hay monto ingresado
        if (chkEfectivo.checked && montoInput.value) calcularVuelto();
        actualizarEstadoPago();
    }

    function mostrarVacio() {
        document.getElementById('tbodyPos').innerHTML = `
            <tr id="rowVacio">
                <td colspan="3" class="text-center text-muted py-5">
                    <i class="bi bi-upc-scan d-block mb-2" style="font-size:2.5rem;"></i>
                    Escanee o busque un producto para comenzar.
                </td>
            </tr>`;
        actualizarEstadoPago();
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function toast(msg, tipo) {
        const stack = document.getElementById('toastStack');
        const el = document.createElement('div');
        el.className = `toast-item toast-${tipo}`;
        el.textContent = msg;
        stack.appendChild(el);
        requestAnimationFrame(() => { requestAnimationFrame(() => el.classList.add('show')); });
        setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 300); }, 2500);
    }

    // Estado inicial del botón (puede haber items si venía de session)
    actualizarEstadoPago();
});
</script>
@endsection
