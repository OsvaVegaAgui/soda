@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
                    <i class="bi bi-cash-coin me-1"></i>Ingresar Caja de Hoy
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
                                <th class="text-end">Monto</th>
                                <th>Observación</th>
                                <th>Registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cajas as $caja)
                            <tr>
                                <td class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($caja->fecha)->translatedFormat('d/m/Y') }}
                                </td>
                                @if($user->rol === 1)
                                <td>{{ $caja->user?->name ?? '—' }}</td>
                                @endif
                                <td class="text-end fw-bold text-success">
                                    ₡{{ number_format($caja->monto, 2) }}
                                </td>
                                <td class="text-muted small">{{ $caja->observacion ?? '—' }}</td>
                                <td class="small text-muted">
                                    {{ $caja->created_at->format('d/m/Y H:i') }}
                                    @if($caja->updated_at->ne($caja->created_at))
                                        <br><span class="badge bg-warning text-dark">Actualizado</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $user->rol === 1 ? 5 : 4 }}" class="text-center text-muted py-5">
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
