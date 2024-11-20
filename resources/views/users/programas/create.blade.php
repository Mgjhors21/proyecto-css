@extends('template')

@section('contenido')

    <link rel="stylesheet" href="{{ asset('css/create_programa.css') }}">

    <div class="card">
        <div class="card-header">
            <h1>Crear Programa para Facultad: {{ $facultad->nombre_facultad }}</h1>
        </div>
        <!-- Mostrar alertas de errores -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> Existen algunos problemas con los datos ingresados.
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('programas.store', $facultad->id) }}" method="POST" id="programaForm">
            @csrf
            <input type="hidden" name="facultad" value="{{ $facultad->id }}">

            <div class="form-group">
                <label for="codigo_programa">Código de Programa</label>
                <input type="text" class="form-control" id="codigo_programa" name="codigo_programa" required>
                <small id="codigo_programa_help" class="form-text text-muted">Ingrese un código único que puede incluir solo
                    números.</small>
                @error('codigo_programa')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nombre_programa">Nombre de Programa</label>
                <input type="text" class="form-control" id="nombre_programa" name="nombre_programa" required>
                <small id="nombre_programa_help" class="form-text text-muted">Ingrese el nombre del programa usando solo
                    letras.</small>
                @error('nombre_programa')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="anio_pensum">Año de Pensum</label>
                <input type="text" class="form-control" id="anio_pensum" name="anio_pensum" required>
                <small id="anio_pensum_help" class="form-text text-muted">Ingrese el año en formato numérico (por ejemplo,
                    2024).</small>
                @error('anio_pensum')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipo_programa">Tipo de Programa</label>
                <input type="text" class="form-control" id="tipo_programa" name="tipo_programa" required>
                <small id="tipo_programa_help" class="form-text text-muted">Ingrese el tipo de programa usando solo
                    letras.</small>
                @error('tipo_programa')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipo_grado">Tipo de Grado</label>
                <input type="text" class="form-control" id="tipo_grado" name="tipo_grado" required>
                <small id="tipo_grado_help" class="form-text text-muted">Ingrese el tipo de grado usando solo
                    letras.</small>
                @error('tipo_grado')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Crear</button>
        </form>
    </div>

    <!-- Validación en tiempo real y retroalimentación -->
    <script>
        // Función para mostrar el mensaje de validación en tiempo real
        function showHelpMessage(id, message) {
            const helpText = document.getElementById(id);
            helpText.style.color = 'red';
            helpText.innerText = message;
        }

        // Función para restablecer el mensaje de validación
        function resetHelpMessage(id) {
            const helpText = document.getElementById(id);
            helpText.style.color = 'gray';
            helpText.innerText = helpText.getAttribute('data-default');
        }

        // Escuchar cada campo para validar
        document.getElementById('codigo_programa').addEventListener('input', function() {
            const codigoPrograma = this.value;
            if (!/^[a-zA-Z0-9]+$/.test(codigoPrograma)) {
                showHelpMessage('codigo_programa_help', 'El código debe ser alfanumérico y sin espacios.');
            } else {
                resetHelpMessage('codigo_programa_help');
            }
        });

        document.getElementById('anio_pensum').addEventListener('input', function() {
            const anioPensum = this.value;
            if (!/^\d{4}$/.test(anioPensum)) {
                showHelpMessage('anio_pensum_help', 'El año debe tener 4 dígitos (por ejemplo, 2020).');
            } else {
                resetHelpMessage('anio_pensum_help');
            }
        });
    </script>
@endsection
