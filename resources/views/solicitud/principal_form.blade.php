@extends('template')

@section('titulo', 'Solicitud de Validación de Cursos de Extensión')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/form_principal.css') }}">
    <script src="{{ asset('js/formulario_principal.js') }}"></script> <!-- Archivo externo de JS -->
    <div class="card">
        <h5 class="card-title">Formulario de registro</h5>

        <form method="POST" action="{{ route('estudiante.guardar', $user->id) }}" class="main-form">
            @csrf
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="{{ $nombre }}"
                            readonly required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="last_name">Apellido:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control"
                            value="{{ $last_name }}" readonly required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="cedula">Cédula:</label>
                        <input type="text" id="cedula" name="cedula" class="form-control" value="{{ $cedula }}"
                            readonly required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="celular">Celular:</label>
                        <input type="text" id="celular" name="celular" class="form-control" value="{{ $celular }}"
                            readonly required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" id="correo" name="correo" class="form-control"
                            value="{{ auth()->user()->email }}" readonly required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="codigo_estudiante">Código Estudiante:</label>
                        <input type="text" id="codigo_estudiante" name="codigo_estudiante" class="form-control"
                            value="{{ $codigo_estudiante }}" readonly required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="Facultad">Facultad:</label>
                    <select id="Facultad" name="Facultad" class="form-control" required>
                        <option value="">Seleccione una facultad</option>
                        @foreach ($facultad as $facultades)
                            <option value="{{ $facultades->id }}">{{ $facultades->nombre_facultad }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="programa_academico">Programa Académico:</label>
                    <select id="programa_academico" name="programa_academico" class="form-control" required>
                        <option value="">Seleccione un programa</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        </form>

        <div class="register-section mt-4">
            <h2>Registrar Cursos/Seminarios</h2>
            <a href="{{ route('curso.registrar', ['categoria' => 'curso_seminarios']) }}" class="btn btn-success">Registrar
                Curso</a>

            <div id="lista-registros" class="records-section mt-4">
                <h3>Registros Anteriores</h3>

                @php
                    $cursoController = new \App\Http\Controllers\CursoController();
                    $cursos = $cursoController->obtenerCursosUsuario();
                @endphp

                @if (empty($cursos))
                    <div class="alert alert-info">
                        No hay cursos o seminarios registrados.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Lugar</th>
                                    <th>Horas</th>
                                    <th>Institución</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Archivo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cursos as $curso)
                                    <tr>
                                        <td>{{ $curso['tipo'] }}</td>
                                        <td>{{ $curso['lugar'] }}</td>
                                        <td>{{ $curso['horas'] }}</td>
                                        <td>{{ $curso['institucion'] }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $curso['estado'] === 'aprobado' ? 'bg-success' : ($curso['estado'] === 'pendiente' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ ucfirst($curso['estado']) }}
                                            </span>
                                        </td>
                                        <td>{{ $curso['fecha'] }}</td>
                                        <td>
                                            @if ($curso['archivo'])
                                                <a href="{{ asset('storage/' . $curso['archivo']) }}" target="_blank"
                                                    class="btn btn-sm btn-primary">
                                                    Ver archivo
                                                </a>
                                            @else
                                                <span class="text-muted">Sin archivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('curso.eliminar', $curso['id']) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-danger btn-sm d-flex align-items-center gap-1">
                                                    <i class="fas fa-trash-alt"></i>
                                                    <span>Eliminar</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <form action="{{ route('tickets.crear') }}" method="POST" enctype="multipart/form-data"
                            class="form-ticket mr-2">
                            @csrf
                            <button type="submit" class="btn btn-primary">Crear Ticket</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endsection
