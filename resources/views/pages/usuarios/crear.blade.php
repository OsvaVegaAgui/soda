
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

                    <div style="display: none" id="mensaje" class="alert alert-info" role="alert">
                        <strong>Información</strong> almacenada satisfactoriamente.
                    </div>

<div class="card-body px-5 py-4">

               <form id="formUsuarios" action="{{ route('usuarios', ['accion' => 'insertar']) }}" method="POST">
                    @csrf
                    

                    <!-- Nombre -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" name="name" class="form-control form-control-lg" 
                                   placeholder="Ej: Osvaldo Salazar" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white">
                                <i class="bi bi-envelope-fill"></i>
                            </span>
                            <input type="email" name="email" class="form-control form-control-lg" 
                                   placeholder="ejemplo@correo.com" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-danger text-white">
                                <i class="bi bi-shield-lock-fill"></i>
                            </span>
                            <input type="password" name="password" class="form-control form-control-lg" 
                                   placeholder="Escribe una contraseña…" required>
                        </div>
                    </div>

                    <!-- Rol -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Rol del Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-warning text-dark">
                                <i class="bi bi-person-badge-fill"></i>
                            </span>
                            <select name="rol" class="form-select form-select-lg" required>
                                <option value="">Seleccionar rol…</option>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                            </select>
                        </div>
                    </div>


                    <!-- Botón -->
                    <div class="text-center mt-4">
                       <button class="btn text-white px-4 py-2"
                            style="
                                border-radius: 8px; 
                                font-size: 0.95rem;
                                background: linear-gradient(135deg, #28a745, #20c997);
                                transition: .25s ease;
                            "
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Crear Usuario
                        </button>
                    </div>
                 

                    
                </form>

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
    @vite('resources/assets/js/usuarios.js')

    <script>
    window.rutaLogin = "{{ route('usuarios', ['accion' => 'login']) }}";
    window.rutaSendCode = "{{ route('usuarios', ['accion' => 'send-code']) }}";
    window.rutaValidarToken = "{{ route('usuarios', ['accion' => 'validar-token']) }}";
    window.rutaCambiarContrasena = "{{ route('usuarios', ['accion' => 'cambiar-contrasena']) }}";
     window.rutaUsuariosLista = "{{ url('usuarios/lista') }}";
    </script>

@endsection
