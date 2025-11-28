@extends('layouts.custom-master')

@php
$bodyClass = 'authentication-background';
@endphp

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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

    
    /* Fondo glass */
    .modal-content.cyber-modal {
        background: rgba(20, 20, 35, 0.55);
        backdrop-filter: blur(15px);
        border-radius: 20px !important;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 0 25px rgba(0,0,0,0.4);
        position: relative;
        overflow: hidden;
        animation: cyberPop .35s ease-out;
    }

    /* Glow exterior suave */
    .modal-content.cyber-modal::before {
        content: "";
        position: absolute;
        top: -40%;
        left: -20%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at top left,
            rgba(0, 255, 255, 0.25),
            rgba(108, 92, 231, 0.15),
            transparent 60%);
        filter: blur(50px);
        z-index: -1;
    }

    @keyframes cyberPop {
        from { transform: translateY(30px) scale(0.95); opacity: 0; }
        to   { transform: translateY(0) scale(1); opacity: 1; }
    }

    /* Título gradient neon */
    .cyber-title {
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: .5px;
        background: linear-gradient(90deg, #09fbd3, #08f, #a55bff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Label */
    .cyber-label {
        font-weight: 600;
        color: #dfe6ff;
        margin-bottom: .3rem;
    }

    /* Input futurista */
    .cyber-input {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 12px 14px;
        border-radius: 14px;
        color: #fff;
        transition: all .3s ease;
    }

    .cyber-input:focus {
        border-color: #09fbd3;
        box-shadow: 0 0 12px #09fbd388;
        background: rgba(255,255,255,0.12);
    }

    /* Botón neon */
    .cyber-btn {
        width: 100%;
        padding: 12px;
        font-weight: 700;
        border-radius: 14px;
        background: linear-gradient(90deg, #00f2fe, #4facfe);
        border: none;
        color: white;
        text-transform: uppercase;
        letter-spacing: .8px;
        box-shadow: 0 0 15px rgba(79,172,254,0.55);
        transition: all .25s ease;
    }

    .cyber-btn:hover {
        transform: scale(1.03);
        box-shadow: 0 0 25px rgba(79,172,254,0.9);
    }

    /* Cancel button */
    .cyber-cancel {
        background: rgba(255,255,255,0.1);
        color: #ddd;
        border-radius: 14px;
        padding: 10px 16px;
        border: 1px solid rgba(255,255,255,0.15);
    }

    .cyber-cancel:hover {
        background: rgba(255,255,255,0.2);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-6 col-sm-8 col-12">
      <div class="card custom-card border-0 my-4">
        <div class="card-body p-5">

          <div class="mb-4 text-left">
            <a href="{{ url('index') }}" class="text-success fs-1 d-inline-block" style="text-align: left;">
              <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="#8e44ad" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c3.314 0 6 2.239 6 5v1H6v-1c0-2.761 2.686-5 6-5z" />
                <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>
          </div>

          <div class="text-center">
            <h4 class="mb-1 fw-semibold">¡Hola, bienvenido de nuevo!</h4>
            <p class="mb-4 text-muted fw-normal">Por favor ingresa tus credenciales</p>
          </div>

          
         <form id="formLog" action="{{ route('usuarios', ['accion' => 'loginConfirmacion']) }}" method="POST">
         @csrf

            <div class="mb-3">
              <label for="signin-email" class="form-label text-default">Correo electrónico</label>
              <input type="text" class="form-control" name="email" id="signin-email"
                     placeholder="Ingresa tu correo" value="">
            </div>

            <div class="mb-3">
              <label for="signin-password" class="form-label text-default">Contraseña</label>
              <input type="password" class="form-control" name="password" id="signin-password"
                     placeholder="Ingresa tu contraseña" value="">
            </div>

            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" id="defaultCheck1" checked>
              <label class="form-check-label" for="defaultCheck1">
                Recordarme
              </label>
              <a href="javascript:void(0);" class="float-end link-danger fw-medium fs-12" data-bs-toggle="modal" data-bs-target="#modalRecuperar">
                  ¿Olvidaste tu contraseña?
            </a>
            </div>
            </form>

            

            <form id="formRecuperar">
              <div class="modal fade" id="modalRecuperar" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content cyber-modal">

                          <!-- Header -->
                          <div class="modal-header border-0">
                              <h5 style="color: #fff;"> Recupera tu Contraseña</h5>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>

                          <!-- Body -->
                          <div class="modal-body">
                              <p class="text-light opacity-75 mb-3">
                                  Ingresá tu correo electrónico y recibirás un enlace seguro para restablecerla.
                              </p>

                              <label class="cyber-label" for="recuperarEmail">Correo electrónico</label>
                              <input type="email" id="recuperarEmail" class="form-control cyber-input mb-3"
                                    placeholder="ejemplo@correo.com" required>
                          </div>

                          <!-- Footer -->
                          <div class="modal-footer border-0 d-flex justify-content-between gap-3">
                              <button type="button" class="cyber-cancel flex-fill" data-bs-dismiss="modal">
                                  Cancelar
                              </button>

                              <button type="button" id="btnEnviarRecuperacion" class="cyber-btn flex-fill">
                                  Enviar enlace
                              </button>
                          </div>
                      </div>
                  </div>
              </div>
          </form>

            
            <div class="d-grid mt-4">
              <!-- 🔹 type="button" evita envío GET -->
              <button type="button" id="click" class="btn btn-primary w-100">
                Iniciar sesión
              </button>
            </div>
        

          <div class="text-center my-3 authentication-barrier">
            <span class="op-4 fs-13">O</span>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<script src="{{ asset('build/assets/show-password.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





<script>
    window.rutaLoginConfirmacion = "{{ route('usuarios', ['accion' => 'loginConfirmacion']) }}";
</script>


<script>
    window.rutaLogin = "{{ route('usuarios', ['accion' => 'login']) }}";
    window.rutaSendCode = "{{ route('usuarios', ['accion' => 'send-code']) }}";
    window.rutaValidarToken = "{{ route('usuarios', ['accion' => 'validar-token']) }}";
    window.rutaCambiarContrasena = "{{ route('usuarios', ['accion' => 'cambiar-contrasena']) }}";
</script>



@vite('resources/assets/js/usuarios.js')







@endsection
