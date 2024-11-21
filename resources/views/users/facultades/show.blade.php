@extends('template')

@section('titulo', 'Información de la Facultad')

@section('contenido')
    <!-- Estilos y Scripts -->
    <link rel="stylesheet" href="{{ asset('css/Lista_programas.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/Facultades_programas.js') }}"></script>

    <!-- Tarjeta principal -->
    <div class="card my-5">
        <!-- Encabezado -->
        <div class="card-header text-center">
            <h1 class="mb-0">{{ $facultad->nombre_facultad }}</h1>
            <p class="mb-0 text-light">Programas vinculados a esta facultad</p>
            <div class="logo">
                <i class="bi bi-buildings-fill fs-1"></i>
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="card-body py-5">
            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-file-earmark-text-fill me-2"></i>Nombre del Programa</th>
                            <th><i class="bi bi-calendar-check-fill me-2"></i>Año de Pensum</th>
                            <th><i class="bi bi-gear-fill me-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programas as $programa)
                            <tr>
                                <!-- Nombre del Programa -->
                                <td>{{ $programa->nombre_programa }}</td>

                                <!-- Año de Pensum -->
                                <td>{{ $programa->anio_pensum }}</td>

                                <!-- Acciones -->
                                <td>
                                    <!-- Formulario de eliminación -->
                                    <form action="{{ route('programas.destroy', $programa->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este programa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-delete">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                        <!-- Botón de Volver (posicionado en la esquina superior izquierda) -->
                                        <a href="{{ route('users.facultades') }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Volver

                                            <!-- Mensaje de éxito oculto -->
                                            @if (session('success'))
                                                <div id="success-message" class="d-none">{{ session('success') }}</div>
                                            @endif
                                    </form>


                                </td>
                            </tr>
                        @empty
                            <!-- Sin programas -->
                            <tr>
                                <td colspan="3" class="text-center text-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>No hay programas disponibles para
                                    esta facultad.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>
@endsection
