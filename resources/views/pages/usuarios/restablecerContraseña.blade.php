
@extends('layouts.custom-master')

@php
// Passing the bodyClass variable from the view to the layout
$bodyClass = 'authentication-background';
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('styles')
<style>
    body {
        background: linear-gradient(270deg, #00b894, #0984e3, #6c5ce7, #fd79a8);
        background-size: 800% 800%;
        animation: gradientFlow 15s ease infinite;
        min-height: 100vh;
    }

    @keyframes gradientFlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
</style>
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-6 col-sm-8 col-12">
      <div class="card custom-card border-0 my-4">
        <div class="card-body p-5">

          <!-- ICONO -->
          <div class="mb-4 text-left">
            <a href="{{ url('index') }}" class="text-success fs-1 d-inline-block" style="text-align: left;">
              <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="none"
                   viewBox="0 0 24 24" stroke="#8e44ad" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 14c3.314 0 6 2.239 6 5v1H6v-1c0-2.761 2.686-5 6-5z" />
                <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>
          </div>

          <!-- TITULO -->
          <div class="text-center">
            <h4 class="mb-1 fw-semibold">Restablecer contraseña</h4>
            <p class="mb-4 text-muted fw-normal">Ingresa tu nueva contraseña</p>
          </div>

          <!-- FORMULARIO -->
          <form id="formCambiarContra" onsubmit="return false;">
            @csrf

            
            <input type="hidden" id="email" name="email" value="{{ request('email') }}">
            <input type="hidden" id="token" name="token" value="{{ request('token') }}">

            <div class="mb-3">
              <label class="form-label text-default">Nueva contraseña</label>
              <input type="password" class="form-control" id="newPassword"
                     placeholder="Ingresa tu nueva contraseña" required>
            </div>

            <div class="mb-3">
              <label class="form-label text-default">Confirmar contraseña</label>
              <input type="password" class="form-control" id="confirmPassword"
                     placeholder="Repite tu contraseña" required>
            </div>

            <div class="d-grid mt-4">
              <button type="button" id="btnCambiar" name="btnCambiar" class="btn btn-primary w-100">
                Cambiar contraseña
              </button>
            </div>
          </form>

          <div class="text-center mt-3 fw-medium">
            <a href="{{ url('login') }}" class="text-primary">Volver al inicio de sesión</a>
          </div>

          <div id="mensaje" style="margin-top: 15px; font-weight: bold;"></div>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection


@section('scripts')

<script>
    window.rutaLogin = "{{ route('usuarios', ['accion' => 'login']) }}";
    window.rutaSendCode = "{{ route('usuarios', ['accion' => 'send-code']) }}";
    window.rutaValidarToken = "{{ route('usuarios', ['accion' => 'validar-token']) }}";
    window.rutaCambiarContrasena = "{{ route('usuarios', ['accion' => 'cambiar-contrasena']) }}";
</script>

        <!-- Show Password JS -->
        <script src="{{asset('build/assets/show-password.js')}}"></script>


        @vite('resources/assets/js/usuarios.js')

        
@endsection