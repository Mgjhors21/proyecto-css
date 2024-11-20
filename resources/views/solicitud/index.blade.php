@extends('template')

@section('titulo', 'Gestión de Tickets')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">

    <div class="content-inner">
        <div class="page-header">
            <h1 class="page-title">Gestión de Tickets</h1>
            <div class="header-actions">
                <input type="text" class="form-control" placeholder="Buscar tickets...">
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
                <div class="stat-card stat-card--success">
                    <div class="stat-title">Horas Totales</div>
                    <div class="stat-value">
                        {{ $solicitudesPorEstudiante->flatten()->reduce(function ($carry, $ticket) {
                            return $carry + $ticket->ticketCursos->sum('curso_horas');
                        }, 0) }}
                    </div>
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
                            <th>Estado</th>
                            <th>Cursos Solicitados</th>
                            <th>Total Horas</th>
                            <th>Fecha de Creación</th>
                            <th>Detalles</th> <!-- Nueva columna para detalles -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($solicitudesPorEstudiante as $tickets)
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td>#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($ticket->estado_ticket) }}">
                                            {{ ucfirst($ticket->estado_ticket) }}
                                        </span>
                                    </td>
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
                                        <!-- Enlace a la vista de detalles -->
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-details">Ver
                                            Detalles</a>
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
