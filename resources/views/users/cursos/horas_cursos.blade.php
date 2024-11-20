@extends('template')

@section('titulo', 'Configuración de Horas de Cursos')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/form_principal.css') }}">
    <script src="{{ asset('js/formulario_principal.js') }}"></script>

    <div class="card">
        <h5 class="card-title">Configurar Horas y Año de Cursos</h5>

        <!-- Mostrar mensajes de éxito o error -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @elseif (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('cursos.horas.guardar') }}" class="main-form">
            @csrf
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="categoria">Categoría del Curso:</label>
                        <select id="categoria" name="categoria"
                            class="form-control @error('categoria') is-invalid @enderror" required>
                            <option value="curso_seminarios" {{ old('categoria') == 'curso_seminarios' ? 'selected' : '' }}>
                                Curso Seminarios</option>
                            <option value="curso_extension" {{ old('categoria') == 'curso_extension' ? 'selected' : '' }}>
                                Curso Extensión</option>
                        </select>
                        @error('categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="horas_minimas">Número de Horas:</label>
                        <input type="number" id="horas_minimas" name="horas_minimas"
                            class="form-control @error('horas_minimas') is-invalid @enderror"
                            value="{{ old('horas_minimas') }}" required>
                        @error('horas_minimas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="año">Año:</label>
                        <input type="number" id="año" name="año"
                            class="form-control @error('año') is-invalid @enderror" value="{{ old('año', date('Y')) }}"
                            required>
                        @error('año')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        </form>

        <div class="register-section mt-4">
            <h2>Horas Configuradas</h2>

            @if ($horasCategorias->isEmpty())
                <div class="alert alert-info">
                    No hay horas configuradas para los cursos.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Horas Mínimas</th>
                                <!-- Cambié "Horas" por "Horas Mínimas" para que coincida con la migración -->
                                <th>Año</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($horasCategorias as $horasCurso)
                                <tr>
                                    <td>{{ $horasCurso->categoria }}</td>
                                    <td>{{ $horasCurso->horas_minimas }}</td> <!-- Asegúrate de mostrar 'horas_minimas' -->
                                    <td>{{ $horasCurso->año }}</td>
                                    <td>
                                        <form action="{{ route('cursos.horas.eliminar', $horasCurso->id) }}" method="POST"
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
                </div>
            @endif
        </div>
    </div>
@endsection
