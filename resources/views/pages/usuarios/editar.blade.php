@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-xl-8 mx-auto">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Editar Usuario</div>
                <a href="{{ route('usuarios', ['accion' => 'lista']) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <div class="card-body">
                <div id="mensaje" class="alert mb-3" style="display:none" role="alert"></div>

                <form id="formEditarUsuarios"
                      action="{{ route('usuarios', ['accion' => 'editar', 'id' => $usuario->id]) }}"
                      method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $usuario->id }}">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ $usuario->name }}"
                                   placeholder="Ej: Juan Pérez" required maxlength="100">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ $usuario->email }}"
                                   placeholder="ejemplo@correo.com" required maxlength="150">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="(Opcional) Dejar vacío para no cambiar" minlength="4">
                            <small class="text-muted">Si no desea cambiarla, deje este campo vacío.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select name="rol" class="form-select" required>
                                <option value="">Seleccionar rol…</option>
                                <option value="1" {{ $usuario->rol == 1 ? 'selected' : '' }}>Administrador</option>
                                <option value="2" {{ $usuario->rol == 2 ? 'selected' : '' }}>Usuario</option>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-pencil-square me-1"></i> Actualizar Usuario
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite('resources/assets/js/usuarios.js')
    <script>
        window.rutaUsuariosLista = "{{ route('usuarios', ['accion' => 'lista']) }}";
    </script>
@endsection
