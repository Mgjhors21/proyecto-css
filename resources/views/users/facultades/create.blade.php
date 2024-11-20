@extends('template')


@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/facultad_create.css') }}">

    <div class="card">
        <div class="card-header">
            <h1>Crear Facultad</h1>
        </div>
        <!-- Mostrar mensajes de éxito o error -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Formulario para crear la facultad -->
        <form action="{{ route('facultades.store') }}" method="POST">
            @csrf

            <!-- Código de Facultad -->
            <div class="form-group">
                <label for="codigo_facultad">Código de Facultad</label>
                <input type="text" class="form-control" id="codigo_facultad" name="codigo_facultad" required
                    value="{{ old('codigo_facultad') }}">
                <small id="codigo_programa_help" class="form-text text-muted">Ingrese el Codigo en formato numérico.</small>
                <!-- Mostrar errores de validación -->
                @error('codigo_facultad')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nombre de Facultad -->
            <div class="form-group">
                <label for="nombre_facultad">Nombre de Facultad</label>
                <input type="text" class="form-control" id="nombre_facultad" name="nombre_facultad" required
                    value="{{ old('nombre_facultad') }}">
                <small id="codigo_programa_help" class="form-text text-muted">Ingrese el tipo de Programa en solo
                    letras.</small>
                <!-- Mostrar errores de validación -->
                @error('nombre_facultad')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
        </form>
    </div>
@endsection
