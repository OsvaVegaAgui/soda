@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">Editar Producto Soda</div>
                <a href="{{ route('productos-soda', ['accion' => 'lista']) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <div class="card-body">
                <form id="formEditarProducto"
                      action="{{ route('productos-soda', ['accion' => 'editar', 'id' => $soda->id_producto_soda]) }}"
                      method="POST">
                    @csrf
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                   placeholder="Nombre"
                                   value="{{ old('nombre', $soda->nombre) }}"
                                   required maxlength="100">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" min="0" name="precio"
                                       class="form-control" placeholder="0.00"
                                       value="{{ old('precio', $soda->precio) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código Softland</label>
                            <input type="text" name="codigo_softland" class="form-control"
                                   placeholder="Ej: PS-001" maxlength="50"
                                   value="{{ old('codigo_softland', $soda->codigo_softland) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código de Barras</label>
                            <input type="text" name="codigo_barras" class="form-control"
                                   placeholder="Ej: 7501055300021" maxlength="50"
                                   value="{{ old('codigo_barras', $soda->codigo_barras) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-1">Estado <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="activo"
                                       value="1" id="activo1"
                                       {{ old('activo', $soda->activo ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo1">Activo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="activo"
                                       value="0" id="activo0"
                                       {{ old('activo', $soda->activo ? '1' : '0') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo0">Inactivo</label>
                            </div>
                        </div>

                        {{-- Códigos de barras adicionales --}}
                        <div class="col-12 mb-3">
                            <hr class="my-2">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-upc me-1"></i>Códigos de barras adicionales
                                <span class="text-muted fw-normal small ms-1">(opcional)</span>
                            </label>
                            <p class="text-muted small mb-2">
                                Úsalos cuando varios productos comparten el mismo nombre y precio pero tienen códigos distintos
                                (ej: Gaseosa → Coca, Fresca, Fanta).
                            </p>
                            <div id="listaCodigosAdicionales">
                                @foreach($soda->codigosAdicionales as $extra)
                                <div class="d-flex gap-2 mb-2 fila-codigo-adicional">
                                    <input type="text" name="codigos_adicionales[]" class="form-control"
                                           placeholder="Código de barras" maxlength="50"
                                           value="{{ $extra->codigo_barras }}">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-quitar-codigo">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="btnAgregarCodigo">
                                <i class="bi bi-plus-circle me-1"></i>Agregar código
                            </button>
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
    @vite('resources/assets/js/productos-soda2.js')
@endsection
