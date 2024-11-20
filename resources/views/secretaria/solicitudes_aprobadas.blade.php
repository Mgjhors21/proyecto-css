@extends('template')

@section('titulo', 'Solicitudes Aprobadas')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">
    <div class="card">
        <div class="card-header">
            <h1 class="text-center mb-4">Lista Solicitudes</h1>
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

        <!-- Tabla de Solicitudes Aprobadas -->
        <h2 class="text-center">Solicitudes Aprobadas</h2>
        @if ($solicitudesAprobadas->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay solicitudes aprobadas registradas.
            </div>
        @else
            <div class="card shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Codigo</th>
                                    <th>Cédula</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Programa Académico</th>
                                    <th>Radicado de Salida</th>
                                    <th>Estado</th>
                                    <th>Fecha de Aprobación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitudesAprobadas as $solicitud)
                                    <tr>
                                        <td>{{ optional($solicitud->estudiante)->name }}</td>
                                        <td>{{ optional($solicitud->estudiante)->cod_alumno }}</td>
                                        <td>{{ optional($solicitud->estudiante)->documento ?? 'N/A' }}</td>
                                        <td>{{ optional($solicitud->estudiante)->email ?? 'N/A' }}</td>
                                        <td>{{ optional($solicitud->estudiante)->telefonos ?? 'N/A' }}</td>
                                        <td>{{ optional($solicitud->estudiante)->programa_academico ?? 'N/A' }}</td>
                                        <td>{{ $solicitud->numero_radicado_salida ?? 'N/A' }}</td>
                                        <td>
                                            {{ ucfirst($solicitud->estado) }}
                                            <!-- Botón Ver Carta -->
                                            <form action="{{ route('viewCarta') }}" method="GET" style="display:inline;">
                                                <input type="hidden" name="ticket_id" value="{{ $solicitud->id }}">
                                                <button type="submit" class="btn btn-primary btn-sm">Ver Carta</button>
                                            </form>
                                        </td>
                                        <td>{{ $solicitud->updated_at->format('d-m-Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabla de Solicitudes Rechazadas -->
        <h2 class="text-center mt-5">Solicitudes Rechazadas</h2>
        @if ($solicitudesRechazadas->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay solicitudes rechazadas registradas.
            </div>
        @else
            <div class="card shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Codigo</th>
                                    <th>Cédula</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Programa Académico</th>
                                    <th>Radicado de Salida</th>
                                    <th>Estado</th>
                                    <th>Fecha de Rechazo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitudesRechazadas as $solicitud)
                                    <tr>
                                        <td>{{ optional($solicitud->estudiante)->name }}</td>
                                        <td>{{ optional($solicitud->estudiante)->cod_alumno }}</td>
                                        <td>{{ optional($solicitud->estudiante)->documento ?? 'N/A' }}</td>
                                        <td>{{ optional($solicitud->estudiante)->email ?? 'N/A' }}</td>
                                        <td>{{ optional($solicitud->estudiante)->telefonos ?? 'N/A' }}</td>
                                        <td>{{ optional($solicitud->estudiante)->programa_academico ?? 'N/A' }}</td>
                                        <td>{{ $solicitud->ticket->numero_radicado_salida ?? 'N/A' }}</td>
                                        <td>
                                            {{ ucfirst($solicitud->estado) }}
                                            <!-- Botón Ver Carta -->
                                            <form action="{{ route('viewCarta') }}" method="GET" style="display:inline;">
                                                <input type="hidden" name="ticket_id" value="{{ $solicitud->id }}">
                                                <button type="submit" class="btn btn-primary btn-sm">Ver Carta</button>
                                            </form>
                                        </td>
                                        <td>{{ $solicitud->updated_at->format('d-m-Y H:i:s') }}</td>
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
