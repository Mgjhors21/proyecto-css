@extends('template')

@section('titulo', 'Registrar Curso')

@section('contenido')
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/extension.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <div class="card">
        <div class="card-header">
            <h1 class="mb-4">Registrar Curso</h1>
        </div>

        <!-- Formulario de registro de cursos -->
        <form method="POST" action="{{ route('curso.guardar') }}" enctype="multipart/form-data" id="cursoForm">
            @csrf
            <div class="form-group mb-3">
                <label for="tipo_curso" class="form-label">Tipo de Curso:</label>
                <select class="form-control" id="tipo_curso" name="tipo_curso" required>
                    <option value="seminario" {{ $categoria == 'curso_seminarios' ? 'selected' : '' }}>Seminario</option>
                    <option value="extension" {{ $categoria == 'curso_extension' ? 'selected' : '' }}>Extensión</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="lugar_certificado" class="form-label">Lugar donde realizó el certificado:</label>
                <input type="text" class="form-control" id="lugar_certificado" name="lugar_certificado" required>
            </div>

            <div class="form-group mb-3">
                <label for="horas" class="form-label">Horas (Número de horas):</label>
                <input type="number" class="form-control" id="horas" name="horas" required>
            </div>

            <div class="form-group mb-3">
                <label for="institucion" class="form-label">Institución (Opcional):</label>
                <select class="form-control" id="institucion_select" name="institucion">
                    <option value="Otro">Otro</option>
                    @foreach ($instituciones as $institucion)
                        <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                    @endforeach
                </select>

                <!-- Campo que se activa si se selecciona "Otro" -->
                <input type="text" class="form-control mt-2" id="otra_institucion" name="otra_institucion"
                    placeholder="Especifique la institución" style="display: none;">
            </div>

            <div class="form-group mb-3">
                <label for="archivo" class="form-label">Subir Certificado (PDF):</label>
                <input type="file" class="form-control" id="archivo" name="archivo" accept="application/pdf">
            </div>

            <button type="submit" class="btn btn-primary">Registrar Curso</button>
            <a href="{{ url('/principal') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Regresar al Inicio
            </a>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/formulario_cursos.js') }}"></script>
@endsection
