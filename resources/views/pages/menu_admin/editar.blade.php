@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-xl-10 mx-auto">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Editar Menú de {{ ucfirst($tipo) }}
                </div>
                <a href="{{ route('menu_admin', ['accion' => 'seleccionar']) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card-body">
                <div id="mensaje" class="alert mb-3" style="display:none" role="alert"></div>

                <form id="formEditar" method="POST" action="{{ route('menu_admin', ['accion' => 'actualizar']) }}">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ $tipo }}">

                    <div class="row align-items-center mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold mb-0">Día</label>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold mb-0">Platillo</label>
                        </div>
                    </div>

                    @foreach($dias as $dia)
                        <div class="row align-items-center mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control fw-semibold" value="{{ $dia->dia }}" readonly>
                            </div>
                            <div class="col-md-8">
                                <input type="text"
                                       name="menu[{{ $dia->getKey() }}]"
                                       class="form-control"
                                       value="{{ $dia->platillo }}"
                                       required
                                       placeholder="Ingrese el platillo del día...">
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/assets/js/stacy.js')
@endsection
