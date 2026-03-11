@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #tablaUsuarios thead { background-color: #6f42c1; color: #fff; }
        #tablaUsuarios.table-striped tbody tr:nth-of-type(odd) { background-color: #ede7f6; }
        #tablaUsuarios.table-hover tbody tr:hover { background-color: #d1c4e9; }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Lista de Usuarios</div>
                <a href="{{ route('usuarios', ['accion' => 'crear']) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Usuario
                </a>
            </div>

            <div class="card-body">
                <div id="mensaje" class="alert mb-3" style="display:none" role="alert"></div>

                <table id="tablaUsuarios" class="table table-striped table-hover align-middle text-center w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td>{{ $u->name }}</td>
                                <td>
                                    <span class="badge {{ $u->rol == 1 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $u->rol == 1 ? 'Administrador' : 'Usuario' }}
                                    </span>
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="badge {{ $u->activo ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $u->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('usuarios', ['accion' => 'ver-editar', 'id' => $u->id]) }}"
                                       class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('usuarios', ['accion' => 'eliminar', 'id' => $u->id]) }}"
                                          class="form-eliminar-usuario d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-danger btn-eliminar-usuario"
                                                data-nombre="{{ $u->name }}"
                                                title="Eliminar">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No hay usuarios registrados.</td>
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
            $('#tablaUsuarios').DataTable({
                pageLength: 15,
                columnDefs: [{ orderable: false, targets: [6] }],
                language: {
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ usuarios',
                    infoEmpty: 'Sin resultados',
                    zeroRecords: 'No se encontraron usuarios',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                }
            });
        });
    </script>
    @vite('resources/assets/js/usuarios.js')
@endsection
