@extends('template')

@section('titulo', 'Lista de Solicitudes de Tickets')

@section('contenido')

    <link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">

    <div class="card">
        <div class="card-header">
            <h1 class="text-center mb-4">Lista de Solicitudes de Tickets</h1>
        </div>
        <!-- Mensajes de éxito y error -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Buscar por nombre del solicitante...">
                    <button class="btn btn-primary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        @if ($solicitudesPorEstudiante->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay solicitudes de tickets registradas.
            </div>
        @else
            <div class="card shadow">
                <div class="card-header">
                    <h2 class="mb-0 text-center">Solicitudes de Tickets Registradas</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0" id="solicitudesTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Codigo Estudiante</th>
                                    <th>Nombre</th>
                                    <th>Cédula</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Programa Académico</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitudesPorEstudiante as $estudianteId => $solicitudes)
                                    @php
                                        // Obtener la información del estudiante
                                        $estudiante = $solicitudes->first()->estudiante;
                                    @endphp
                                    <tr>
                                        <td>{{ $estudiante->cod_alumno }}</td>
                                        <td>{{ $estudiante->name }} {{ $estudiante->apellido }}</td>
                                        <td>{{ $estudiante->documento }}</td>
                                        <td>{{ $estudiante->email }}</td>
                                        <td>{{ $estudiante->telefonos }}</td>
                                        <td>{{ $estudiante->programa_academico }}</td>
                                        <td>
                                            <a href="{{ route('solicitud.detalles', ['id' => $estudiante->id]) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                    <!-- Aquí añadimos las solicitudes de tickets de cada estudiante -->
                                    <tr class="detalles-{{ $estudianteId }} d-none">
                                        <td colspan="7">
                                            <ul>
                                                @foreach ($solicitudes as $solicitud)
                                                    <li>
                                                        Ticket ID: {{ $solicitud->id }} - Estado:
                                                        {{ ucfirst($solicitud->estado) }} - Fecha:
                                                        {{ $solicitud->created_at->format('d-m-Y') }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center mt-4">
            <a href="{{ url('/welcome') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Regresar al Inicio
            </a>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection
