
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
	
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Ingresar Paises
                </div>
               
            </div>
            <div class="card-body">
                <form id="formCrear" method="POST" action="{{ route('paises.procesar', ['accion' => 'insertar']) }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="txtName" name="txtName" placeholder="Nombre" aria-label="Nombre">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cantidad de habitantes</label>
                            <input type="number" class="form-control" placeholder="Habitantes" id="txtHabitantes" name="txtHabitantes" aria-label="Habitantes">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Independencia</label>
                            <input type="date" id="txtFecha" name="txtFecha" class="form-control" aria-label="Independencia ">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Extension</label>
                            <input type="number" id="txtExtension" name="txtExtension" class="form-control" placeholder="Extension" aria-label="Extension">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>

                        <div style="display: none" id="mensaje" class="alert alert-info" role="alert">
                            <strong>Información</strong> almacenada satisfactoriamente.
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer d-none border-top-0"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
        
    @vite('resources/assets/js/datatables.js')

    @vite('resources/assets/js/paises.js')

@endsection
