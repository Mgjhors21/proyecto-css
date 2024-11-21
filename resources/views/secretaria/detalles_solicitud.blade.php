@extends('template')

@section('titulo', 'Detalles de Solicitudes')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/detalles_solicitud.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('contenido')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalles de Solicitudes</h1>
            <a href="{{ route('solicitudes_lista') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Lista
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-user-graduate"></i> {{ $estudiante->nombre }} {{ $estudiante->last_name }}
                </h4>
            </div>
        </div>

        @if ($solicitudes->isEmpty())
            <div class="alert alert-info text-center fade-in">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">No hay solicitudes registradas para este estudiante.</p>
            </div>
        @else
            @foreach ($solicitudes as $solicitud)
                <div class="card shadow-sm mb-4 fade-in">
                    <div class="card-header bg-white ticket-header d-flex justify-content-between">
                        <div>
                            <i class="fas fa-hashtag text-muted"></i> ID: {{ $solicitud->id }}
                            <span class="mx-2">|</span>
                            <i class="fas fa-calendar-alt text-muted"></i> {{ $solicitud->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <span class="badge estado-{{ strtolower($solicitud->estado_ticket) }}">
                                {{ $solicitud->estado_ticket }}
                            </span>
                        </div>
                        <div>
                            <!-- Botones para aprobar/rechazar el ticket -->
                            @if ($solicitud->ticketCursos->where('estado_curso', 'pendiente')->isEmpty())
                                @if ($solicitud->estado_ticket === 'pendiente')
                                    <form action="{{ route('ticket.aprobar', ['ticketId' => $solicitud->id]) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm btn-aprobar">
                                            <i class="fas fa-check"></i> Aprobar Ticket
                                        </button>
                                    </form>
                                    <form action="{{ route('solicitud.rechazar', ['ticketId' => $solicitud->id]) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm btn-rechazar">
                                            <i class="fas fa-times"></i> Rechazar Ticket
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p class="text-muted">Debes procesar todos los cursos antes de aprobar o rechazar el ticket.
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="mb-3">Cursos Solicitados</h5>
                        @if ($solicitud->ticketCursos->isEmpty())
                            <p class="text-muted">No hay cursos vinculados a esta solicitud</p>
                        @else
                            @foreach ($solicitud->ticketCursos as $curso)
                                <div class="curso-card mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="text-primary mb-0">{{ $curso->curso_nombre }}</h5>
                                        <span class="badge estado-{{ strtolower($curso->estado_curso) }}">
                                            {{ ucfirst($curso->estado_curso) }}
                                        </span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clock text-muted me-2"></i>
                                                <span>{{ $curso->curso_horas }} horas</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar text-muted me-2"></i>
                                                <span>{{ $curso->curso_fecha }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle text-muted me-2"></i>
                                                <span>{{ $curso->curso_descripcion ?? 'Sin descripción' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="archivos-section mt-3">
                                        <h6>Archivos</h6>
                                        <div class="archivo-card">
                                            <i class="fas fa-file-alt text-primary me-2"></i> Archivo
                                            @if ($curso->archivo_seminario || $curso->archivo_extension)
                                                <div class="btn-group ms-3">
                                                    @if ($curso->archivo_seminario)
                                                        <a href="{{ asset('storage/' . $curso->archivo_seminario) }}"
                                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                        <a href="{{ asset('storage/' . $curso->archivo_seminario) }}"
                                                            download class="btn btn-outline-success btn-sm">
                                                            <i class="fas fa-download"></i> Descargar
                                                        </a>
                                                    @elseif ($curso->archivo_extension)
                                                        <a href="{{ asset('storage/' . $curso->archivo_extension) }}"
                                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                        <a href="{{ asset('storage/' . $curso->archivo_extension) }}"
                                                            download class="btn btn-outline-success btn-sm">
                                                            <i class="fas fa-download"></i> Descargar
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-secondary ms-3">Sin archivo</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="action-buttons mt-3">
                                        @if ($curso->estado_curso === 'pendiente')
                                        <form action="{{ route('curso.aprobar', ['id' => $curso->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm btn-aprobar-curso">
                                                <i class="fas fa-check"></i> Aprobar Curso
                                            </button>
                                        </form>
                                            <form
                                                action="{{ route('curso.rechazar', ['id' => $curso->id, 'tipoCurso' => 'extension']) }}"
                                                method="POST" id="form-rechazar-{{ $curso->id }}" class="d-inline">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm btn-rechazar-curso"
                                                    data-curso-id="{{ $curso->id }}">
                                                    <i class="fas fa-times"></i> Agregar Comentario
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/detalles_solicitud.js') }}"></script>
@endsection
