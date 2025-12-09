
@extends('layouts.master')

@section('styles')

    {{-- CSS PARA FECHA BONITA --}}
    <link rel="stylesheet" href="{{asset('build/assets/libs/flatpickr/flatpickr.min.css')}}">

    {{-- SELECT PARA BUSCAR --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- CSS PARA VALIDACION DE CAMPOS -->
    <link rel="stylesheet" href="{{asset('build/assets/libs/prismjs/themes/prism-coy.min.css')}}">

    {{-- CSS PARA TABLAS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">

@endsection

@section('content')


<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-header bg-primary text-white py-3" style="border-radius: 15px 15px 0 0;">
        <h5 class="mb-0">
            <i class="bi bi-people-fill me-2"></i>Lista de Usuarios
        </h5>
    </div>

    <div class="card-body p-3">

        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">

                <thead class="table-light">
                    <tr>
                        <th>Id Usuario</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($usuarios as $u)
                    <tr>
                        <td>{{ $u->id }}</td>

                        <td class="fw-semibold">
                            <i class="bi bi-person-circle text-primary me-1"></i>
                            {{ $u->name }}
                        </td>

                        <td>
                            <span class="badge 
                               {{ $u->rol == 1 ? 'bg-danger' : 'bg-success' }}">
                                {{ $u->rol == 1 ? 'Administrador' : 'Usuario' }}
                            </span>
                        </td>

                        <td>{{ $u->email }}</td>

                        <td>
                            @if($u->activo == 1)
                                <span class="badge" style="background:#0000FF; color:white;">
                                    Activo
                                </span>
                            @else
                                <span class="badge bg-dark">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</td>

                        <td>
                            <a href="{{ route('usuarios', ['accion' => 'ver-editar', 'id' => $u->id]) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square"></i>
                            </a>

                            <a href="{{ route('usuarios', ['accion' => 'eliminar', 'id' => $u->id]) }}" 
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>



	


@endsection

@section('scripts')
        
    {{-- JS PARA FECHA BONITA --}}
    <script src="{{asset('build/assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    @vite('resources/assets/js/date&time_pickers.js')

    {{-- JS PARA SELECT QUE BUSCA --}}
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @vite('resources/assets/js/select2.js')

    <!-- JS PARA VALIDACIONES -->
    <script src="{{asset('build/assets/libs/prismjs/prism.js')}}"></script>
    @vite('resources/assets/js/prism-custom.js')
    @vite('resources/assets/js/validation.js')


    {{-- jJS PARA TABLAS --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script> --}}
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    @vite('resources/assets/js/datatables.js')

@endsection

