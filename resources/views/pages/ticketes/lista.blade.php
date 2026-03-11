@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #tablaTickets thead { background-color: #6f42c1; color: #fff; }
        #tablaTickets.table-striped tbody tr:nth-of-type(odd) { background-color: #ede7f6; }
        #tablaTickets.table-hover  tbody tr:hover              { background-color: #d1c4e9; }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Lista de Tiquetes</div>
                <a href="{{ route('ticketes-soda', ['accion' => 'crear']) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Tiquete
                </a>
            </div>

            <div class="card-body">
                <div id="mensaje" class="alert mb-3" style="display:none" role="alert"></div>

                <table id="tablaTickets" class="table table-striped table-hover align-middle text-center w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id_ticket }}</td>
                                <td class="text-start">{{ $ticket->nombre }}</td>
                                <td><code>{{ $ticket->codigo }}</code></td>
                                <td>
                                    <span class="badge" style="background-color:#6f42c1">
                                        {{ $ticket->categoria->nombre ?? '-' }}
                                    </span>
                                </td>
                                <td>₡{{ number_format($ticket->precio, 2) }}</td>
                                <td>
                                    <a href="{{ route('ticketes-soda', ['accion' => 'ver-editar', 'id' => $ticket->id_ticket]) }}"
                                       class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('ticketes-soda', ['accion' => 'eliminar', 'id' => $ticket->id_ticket]) }}"
                                          class="form-eliminar-ticket d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-danger btn-eliminar-ticket"
                                                data-nombre="{{ $ticket->nombre }}"
                                                title="Eliminar">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No hay tiquetes registrados.</td>
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
            $('#tablaTickets').DataTable({
                pageLength: 15,
                columnDefs: [{ orderable: false, targets: [5] }],
                language: {
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ tiquetes',
                    infoEmpty: 'Sin resultados',
                    zeroRecords: 'No se encontraron tiquetes',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                }
            });
        });
    </script>
    @vite('resources/assets/js/ticketes.js')
@endsection
