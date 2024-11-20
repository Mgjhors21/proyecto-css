@extends('template')

@section('titulo', 'Registrar Institución')

@section('contenido')

    <link rel="stylesheet" href="{{ asset('css/create_institucion.css') }}">

    <div class="card">
        <div class="card-header">
            <h1 class="mb-4">Registrar Nueva Institución</h1>
        </div>

        <!-- Formulario para agregar una institución -->
        <form method="POST" action="{{ route('institucion.store') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="institucion_nombre" class="header h2">Nombre de la Institución:</label>
                <input type="text" class="form-control" id="institucion_nombre" name="nombre" required>
            </div>


            <div class="buttoms">
                <button type="submit" class="btn btn-primary">Registrar Institución</button>
                <a href="{{ url('/principal') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Regresar al Inicio
                </a>
            </div>
            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mensaje de error -->
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif



        </form>

        <!-- Lista de instituciones registradas -->
        <div class="mt-4">
            <h3>Instituciones Registradas</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($instituciones as $institucion)
                        <tr>
                            <td>{{ $institucion->nombre }}</td>
                            <td>
                                <!-- Solo el botón para eliminar la institución -->
                                <form action="{{ route('institucion.destroy', $institucion->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
