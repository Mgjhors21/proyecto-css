@extends('template')

@section('titulo', 'Lista de Solicitudes de Tickets')

@section('contenido')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/solicitudes.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">

    <div class="card">
        <div class="card-header">
            <h1 class="text-center mb-4">Lista de Solicitudes</h1>
        </div>

        <!-- Mostrar el mensaje de SweetAlert directamente -->
        @if (session('success'))
            <script>
                Swal.fire({
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    title: '¡Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            </script>
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
                    <h2 class="mb-0 text-center">Tickets Registrados</h2>
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
                                        $estudiante = $solicitudes->first()->estudiante;
                                    @endphp
                                    <tr>
                                        <form action="{{ route('estudiante.actualizar', ['id' => $estudiante->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <td>
                                                <input type="text" name="cod_alumno"
                                                    value="{{ old('cod_alumno', $estudiante->cod_alumno) }}"
                                                    class="form-control @error('cod_alumno') is-invalid @enderror" required>
                                                @error('cod_alumno')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="name"
                                                    value="{{ old('name', $estudiante->name) }}"
                                                    class="form-control @error('name') is-invalid @enderror" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="documento"
                                                    value="{{ old('documento', $estudiante->documento) }}"
                                                    class="form-control @error('documento') is-invalid @enderror" maxlength="10"   required>
                                                @error('documento')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="email" name="email"
                                                    value="{{ old('email', $estudiante->email) }}"
                                                    class="form-control @error('email') is-invalid @enderror" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="telefonos"
                                                    value="{{ old('telefonos', $estudiante->telefonos) }}"
                                                    class="form-control @error('telefonos') is-invalid @enderror" maxlength="10">
                                                @error('telefonos')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="programa_academico"
                                                    value="{{ old('programa_academico', $estudiante->programa_academico) }}"
                                                    class="form-control @error('programa_academico') is-invalid @enderror">
                                                @error('programa_academico')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <a href="{{ route('solicitud.detalles', ['id' => $estudiante->id]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver Detalles
                                                </a>
                                                <button type="submit" class="btn btn-success btn-sm guardar-btn">
                                                    <i class="fas fa-save"></i> Guardar
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                    <!-- Solicitudes de tickets -->
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
