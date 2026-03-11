@extends('layouts.master')

@section('styles')
<style>
    #tablaDetalle thead { background: #6f42c1; color: #fff; }
    .info-badge { font-size: .85rem; }
</style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-9 mx-auto">

        {{-- Encabezado de la venta --}}
        <div class="card custom-card mb-3">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    <i class="bi bi-receipt me-2"></i>Detalle de Venta #{{ $venta->id }}
                </div>
                <a href="{{ route('ventas', ['accion' => 'lista']) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-3">
                        <p class="text-muted small fw-semibold text-uppercase mb-1">Fecha</p>
                        <p class="fw-bold mb-0">{{ $venta->fecha ? $venta->fecha->translatedFormat('d \d\e F Y') : '—' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted small fw-semibold text-uppercase mb-1">Cajera</p>
                        <p class="fw-bold mb-0">{{ $venta->user ? $venta->user->name : '—' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted small fw-semibold text-uppercase mb-1">Método de pago</p>
                        <p class="mb-0">
                            @php $mp = $venta->metodo_pago; @endphp
                            @if($mp === 'efectivo')
                                <span class="badge bg-success info-badge"><i class="bi bi-cash me-1"></i>Efectivo</span>
                            @elseif($mp === 'tarjeta')
                                <span class="badge bg-info info-badge"><i class="bi bi-credit-card me-1"></i>Tarjeta</span>
                            @elseif($mp === 'mixto')
                                <span class="badge bg-warning text-dark info-badge"><i class="bi bi-wallet2 me-1"></i>Mixto</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted small fw-semibold text-uppercase mb-1">Total</p>
                        <p class="fw-bold fs-5 mb-0 text-success">
                            ₡{{ number_format($venta->total ?? $venta->detalles->sum('subtotal'), 2) }}
                        </p>
                    </div>

                    @if(in_array($mp, ['efectivo', 'mixto']) && $venta->monto_efectivo !== null)
                    <div class="col-sm-3">
                        <p class="text-muted small fw-semibold text-uppercase mb-1">Efectivo recibido</p>
                        <p class="fw-bold mb-0">₡{{ number_format($venta->monto_efectivo, 2) }}</p>
                    </div>
                    @endif

                    @if($mp === 'efectivo' && $venta->vuelto !== null)
                    <div class="col-sm-3">
                        <p class="text-muted small fw-semibold text-uppercase mb-1">Vuelto entregado</p>
                        <p class="fw-bold mb-0 text-primary">₡{{ number_format($venta->vuelto, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ítems de la venta --}}
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="bi bi-bag me-2"></i>Productos vendidos
                    <span class="badge bg-primary ms-2">{{ $venta->detalles->count() }} ítem(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaDetalle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="width:160px;">Código</th>
                                <th class="text-center" style="width:100px;">Cantidad</th>
                                <th class="text-end" style="width:130px;">P. Unitario</th>
                                <th class="text-end" style="width:130px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($venta->detalles as $d)
                            <tr>
                                <td class="fw-semibold">{{ $d->nombre ?: '(sin nombre)' }}</td>
                                <td class="text-muted">{{ $d->codigo ?: '—' }}</td>
                                <td class="text-center">{{ $d->cantidad_vendida }}</td>
                                <td class="text-end">₡{{ number_format($d->precio_unitario, 2) }}</td>
                                <td class="text-end fw-bold">₡{{ number_format($d->subtotal, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Sin ítems registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($venta->detalles->isNotEmpty())
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">Total</td>
                                <td class="text-end">₡{{ number_format($venta->detalles->sum('subtotal'), 2) }}</td>
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
