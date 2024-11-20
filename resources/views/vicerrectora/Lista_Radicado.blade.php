@extends('template')

@section('titulo', 'Gestión de Tickets')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
    <script src="{{ asset('js/Radicado_Solicitud.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="content-inner">
        <div class="page-header">
            <h1 class="page-title">Gestión de Tickets - Número Radicado</h1>
            <div class="header-actions">
                <!-- Campo de búsqueda -->
                <input type="text" class="form-control" id="search-ticket" placeholder="Buscar tickets por ID...">
                <!-- Botón con icono de lupa -->
                <button id="search-btn" class="btn btn-outline-primary ml-2">
                    <i class="fa fa-search"></i> <!-- Icono de lupa -->
                </button>
            </div>
        </div>

        @if ($solicitudesPorEstudiante->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <h3>No hay tickets registrados</h3>
                <p>Los tickets que crees aparecerán aquí.</p>
            </div>
        @else
            <div class="ticket-stats">
                <div class="stat-card">
                    <div class="stat-title">Total Tickets</div>
                    <div class="stat-value">{{ $solicitudesPorEstudiante->flatten()->count() }}</div>
                </div>
                <div class="stat-card stat-card--danger">
                    <div class="stat-title">Último Ticket</div>
                    <div class="stat-value stat-value--small">
                        {{ $solicitudesPorEstudiante->flatten()->last()->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="tickets-table">
                    <thead>
                        <tr>
                            <th>ID Ticket</th>
                            <th>Cursos Solicitados</th>
                            <th>Total Horas</th>
                            <th>Fecha de Creación</th>
                            <th>Número Radicado</th>
                            <th>Estado Vicerrectoría</th>
                        </tr>
                    </thead>
                    <tbody id="ticket-table-body">
                        @foreach ($solicitudesPorEstudiante as $tickets)
                            @foreach ($tickets as $ticket)
                                <tr data-ticket-id="{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}">
                                    <td>#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="cursos-list">
                                            @foreach ($ticket->ticketCursos as $ticketCurso)
                                                <div class="curso-item">
                                                    <span class="curso-nombre">{{ $ticketCurso->curso_nombre }}</span>
                                                    <span class="curso-horas">({{ $ticketCurso->curso_horas }} hrs)</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ticket-horas">
                                            {{ $ticket->ticketCursos->sum('curso_horas') }}
                                        </span> hrs
                                    </td>
                                    <td>
                                        <div>{{ $ticket->created_at->format('d/m/Y') }}</div>
                                        <small class="ticket-fecha-small">{{ $ticket->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <form action="{{ route('vicerrectora.updateRadicado', $ticket->id) }}"
                                            method="POST">
                                            @csrf
                                            <input type="text" name="numero_radicado" class="form-control"
                                                value="{{ old('numero_radicado', $ticket->numero_radicado) }}"
                                                placeholder="Escribe el número de radicado">
                                            <button type="submit" class="btn btn-sm btn-outline-success mt-2">
                                                Guardar
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($ticket->estado_vicerrectoria) }}">
                                            {{ ucfirst($ticket->estado_vicerrectoria) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
