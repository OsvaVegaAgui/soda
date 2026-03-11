@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #tablaHistorial thead { background-color: #6f42c1; color: #fff; }
        #tablaHistorial.table-striped tbody tr:nth-of-type(odd) { background-color: #ede7f6; }
        #tablaHistorial.table-hover  tbody tr:hover              { background-color: #d1c4e9; }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Historial de Tiquetes Generados</div>
                <a href="{{ route('generar-ticketes', ['accion' => 'crear']) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Generar Tiquetes
                </a>
            </div>

            <div class="card-body">
                <table id="tablaHistorial" class="table table-striped table-hover align-middle text-center w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tiquete</th>
                            <th>Categoría</th>
                            <th>Cantidad Impresa</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $reg)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">
                                    <div class="fw-semibold">{{ $reg->ticket->nombre ?? '-' }}</div>
                                    <small class="text-muted font-monospace">{{ $reg->ticket->codigo ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge" style="background-color:#6f42c1">
                                        {{ $reg->ticket->categoria->nombre ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">{{ $reg->cantidad_impresa }}</span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($reg->fecha_impresion)->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">No hay registros de generación aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
        $(document).ready(function () {
            $('#tablaHistorial').DataTable({
                order: [[4, 'desc']],
                pageLength: 20,
                columnDefs: [{ orderable: false, targets: [0] }],
                language: {
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'Sin resultados',
                    zeroRecords: 'No se encontraron registros',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                }
            });
        });
    </script>
@endsection
