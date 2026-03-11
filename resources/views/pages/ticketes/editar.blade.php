@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Editar Tiquete</div>
                <a href="{{ route('ticketes-soda', ['accion' => 'lista']) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <div class="card-body">
                <form id="formEditarTicket"
                      action="{{ route('ticketes-soda', ['accion' => 'editar', 'id' => $ticket->id_ticket]) }}"
                      method="POST">
                    @csrf
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Tiquete <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                   placeholder="Ej: Almuerzo Corriente"
                                   value="{{ old('nombre', $ticket->nombre) }}"
                                   required maxlength="150">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código Softland <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control"
                                   placeholder="Ej: TK-ALM-001"
                                   value="{{ old('codigo', $ticket->codigo) }}"
                                   required maxlength="50">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_d" class="form-select" required>
                                <option value="">— Seleccione una categoría —</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id_categoria }}"
                                        {{ old('categoria_d', $ticket->categoria_d) == $cat->id_categoria ? 'selected' : '' }}>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" min="0" name="precio"
                                       class="form-control" placeholder="0.00"
                                       value="{{ old('precio', $ticket->precio) }}" required>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Guardar Cambios
                            </button>
                        </div>

                        <div class="col-12 mt-3">
                            <div id="mensaje" class="alert" style="display:none" role="alert"></div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite('resources/assets/js/ticketes.js')
@endsection
