
@extends('layouts.master')

@section('styles')

    {{-- CSS PARA TABLAS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">

@endsection

@section('content')
	

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">
                    Responsive Modal Datatable
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="responsivemodal-DataTable" class="table table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Extension</th>
                                <th>Fecha</th>
                                <th>Habitantes</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($paises as $p)
                                <tr>
                                    <td>{{ $p->idPais }} {{ $p->nombre }}</td>
                                    <td>{{ $p->extension }}</td>
                                    <td>{{ $p->fecha_independencia}}</td>
                                    <td>{{ $p->habitantes}}</td>
                                    
                                    <td>
                                        <a href="{{ route('paises.procesar', ['accion' => 'ver-editar', 'id' => $p->idPais]) }}" class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                    </td>

                                    <td>
                                        <form method="POST"
                                            action="{{ route('paises.procesar', ['accion' => 'eliminar', 'id' => $p->idPais]) }}"
                                            class="form-eliminar-pais d-inline">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger btn-eliminar-pais"
                                                    data-nombre="{{ $p->nombre }}">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>


                                </tr>
                            @endforeach

                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
        
    {{-- jJS PARA TABLAS --}}
    
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

    @vite('resources/assets/js/paises.js')

@endsection
