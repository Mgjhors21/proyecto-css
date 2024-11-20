@extends('template')

@section('titulo', 'Detalles del Curso')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
    <div class="container-fluid py-4">
        <div class="page-header mb-4">
            <h1 class="page-title">Detalles del Ticket #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</h1>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="text-primary">
                    Estado: <span
                        class="status-badge status-{{ strtolower($ticket->estado_ticket) }}">{{ ucfirst($ticket->estado_ticket) }}</span>
                </h3>
                <h4>Fecha de Creación: {{ $ticket->created_at->format('d/m/Y H:i') }}</h4>

                <h5 class="mt-4">Cursos Solicitados:</h5>
                <ul class="list-group">
                    @foreach ($ticket->ticketCursos as $ticketCurso)
                        <li class="list-group-item">
                            <strong>Nombre del Curso:</strong> {{ $ticketCurso->curso_nombre }} <br>
                            <strong>Horas:</strong> {{ $ticketCurso->curso_horas }} <br>
                            <strong>Estado del Curso:</strong>
                            <span
                                class="status-badge status-{{ strtolower($ticketCurso->estado_curso) }}">{{ ucfirst($ticketCurso->estado_curso) }}</span>
                            <br>
                            <strong>Fecha:</strong>
                            {{ $ticketCurso->curso_fecha ? \Carbon\Carbon::parse($ticketCurso->curso_fecha)->format('d/m/Y') : 'N/A' }}
                            <br>
                            <strong>Descripción:</strong> {{ $ticketCurso->curso_descripcion ?? 'N/A' }} <br>

                            @if ($ticketCurso->cursoSeminario)
                                <strong>Curso de Seminario - Institución:</strong>
                                {{ $ticketCurso->cursoSeminario->institucion ?? 'N/A' }} <br>
                            @endif
                            @if ($ticketCurso->cursoExtension)
                                <strong>Curso de Extensión - Institución:</strong>
                                {{ $ticketCurso->cursoExtension->institucion ?? 'N/A' }} <br>
                            @endif

                            <strong>Archivo:</strong>
                            @if ($ticketCurso->archivo_seminario)
                                <a href="{{ asset('storage/' . $ticketCurso->archivo_seminario) }}"
                                    class="link-primary">Seminario</a>
                            @endif
                            @if ($ticketCurso->archivo_extension)
                                <a href="{{ asset('storage/' . $ticketCurso->archivo_extension) }}"
                                    class="link-primary">Extensión</a>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <h4 class="mt-4">Total Horas: <strong>{{ $ticket->ticketCursos->sum('curso_horas') }} hrs</strong></h4>

                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary mt-3">
                    <i class="fas fa-arrow-left"></i> Volver a la lista de tickets
                </a>
            </div>
        </div>
    </div>
@endsection
