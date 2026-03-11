@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Crear Producto Soda</div>
                <a href="{{ route('productos-soda', ['accion' => 'lista']) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <div class="card-body">
                <form id="formCrearProducto"
                      action="{{ route('productos-soda', ['accion' => 'insertar']) }}"
                      method="POST">
                    @csrf
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                   placeholder="Ej: Coca Cola 350ml" required maxlength="100">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" min="0" name="precio"
                                       class="form-control" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código Softland</label>
                            <input type="text" name="codigo_softland" class="form-control"
                                   placeholder="Ej: PS-001" maxlength="50">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código de Barras</label>
                            <input type="text" name="codigo_barras" class="form-control"
                                   placeholder="Ej: 7501055300021" maxlength="50">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-1">Estado <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="activo"
                                       value="1" id="activo1" checked>
                                <label class="form-check-label" for="activo1">Activo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="activo"
                                       value="0" id="activo0">
                                <label class="form-check-label" for="activo0">Inactivo</label>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Agregar Producto
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
    @vite('resources/assets/js/productos-soda2.js')
@endsection
