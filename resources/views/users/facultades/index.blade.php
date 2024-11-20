@extends('template')

@section('titulo', 'Gestión de Facultades')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/Gestion_falcutades.css') }}">

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5>Facultades</h5>
            </div>

            <!-- Botones de Redirección -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <a href="{{ route('facultades.create') }}" class="btn btn-success btn-block w-100">Crear Facultad</a>
                </div>
            </div>

            <!-- Tabla de Facultades -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facultades as $facultad)
                            <tr>
                                <td>{{ $facultad->codigo_facultad }}</td>
                                <td>{{ $facultad->nombre_facultad }}</td>
                                <td class="d-flex gap-2">
                                    <a href="{{ route('facultades.show', $facultad->id) }}"
                                        class="btn btn-info btn-sm">Ver</a>

                                    <form action="{{ route('facultades.destroy', $facultad->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>

                                    <!-- Botón para crear un programa para esta facultad -->
                                    <a href="{{ route('programas.create', ['facultad' => $facultad->id]) }}"
                                        class="btn btn-secondary btn-sm">Crear Programa</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
