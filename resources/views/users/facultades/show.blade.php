@extends('template')
@section('titulo', 'Información de la Facultad')
@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/Lista_programas.css') }}">

    <div class="card my-5">

        <div class="card-header">
            <div>
                <h1 class="mb-0">{{ $facultad->nombre_facultad }}</h1>
                <div>
                    <p class="mb-0 text-light">Programas vinculados a esta facultad</p>
                </div>

                <div class="logo">
                    <i class="bi bi-buildings-fill  fs-1"></i>
                </div>
            </div>
        </div>

        <div class="card-body py-5">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-file-earmark-text-fill me-2"></i>Nombre del Programa</th>
                            <th><i class="bi bi-calendar-check-fill me-2"></i>Año de Pensum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programas as $programa)
                            <tr>
                                <td>{{ $programa->nombre_programa }}</td>
                                <td>{{ $programa->anio_pensum }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>No
                                    hay programas disponibles para esta facultad.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
