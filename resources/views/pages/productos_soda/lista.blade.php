@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #tablaProductos thead { background-color: #6f42c1; color: #fff; }
        #tablaProductos.table-striped tbody tr:nth-of-type(odd) { background-color: #ede7f6; }
        #tablaProductos.table-hover tbody tr:hover { background-color: #d1c4e9; }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Lista de Productos Soda</div>
                <a href="{{ route('productos-soda', ['accion' => 'crear']) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
                </a>
            </div>

            <div class="card-body">
                <div id="mensaje" class="alert mb-3" style="display:none" role="alert"></div>

                <table id="tablaProductos" class="table table-striped table-hover align-middle text-center w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Código Softland</th>
                            <th>Código de Barras</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $producto->id_producto_soda }}</td>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->codigo_softland ?? '-' }}</td>
                                <td>{{ $producto->codigo_barras ?? '-' }}</td>
                                <td>₡{{ number_format($producto->precio, 2) }}</td>
                                <td>
                                    <span class="badge {{ $producto->activo ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('productos-soda', ['accion' => 'ver-editar', 'id' => $producto->id_producto_soda]) }}"
                                       class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('productos-soda', ['accion' => 'eliminar', 'id' => $producto->id_producto_soda]) }}"
                                          class="form-eliminar-producto d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-danger btn-eliminar-producto"
                                                data-nombre="{{ $producto->nombre }}"
                                                title="Eliminar">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No hay productos registrados.</td>
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
            $('#tablaProductos').DataTable({
                pageLength: 15,
                columnDefs: [{ orderable: false, targets: [6] }],
                language: {
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ productos',
                    infoEmpty: 'Sin resultados',
                    zeroRecords: 'No se encontraron productos',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                }
            });
        });
    </script>
    @vite('resources/assets/js/productos-soda2.js')
@endsection
