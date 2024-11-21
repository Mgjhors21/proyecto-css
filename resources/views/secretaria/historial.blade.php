<!-- resources/views/historial/index.blade.php -->
@extends('template')

@section('titulo', 'Historial de Cartas Enviadas')

@section('contenido')

    <link rel="stylesheet" href="{{ asset('css/historial.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


    <div class="card">
        <div class="card-header">
            <h1>Historial de Cartas Enviadas</h1>
        </div>

        <form action="{{ route('historial') }}" method="GET" class="mb-3">
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Buscar por nombre, código o fecha de radicado"
                    value="{{ request('search') }}">
                <button class="btn" type="submit">
                    <i class="fas fa-search"></i> <!-- Ícono de lupa -->
                </button>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Programa</th>
                    <th>Radicado Entrada</th>
                    <th>Radicado de Salida</th>
                    <th>Cursos</th>
                    <th>Estado</th>
                    <th>Fecha de Envío</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historial as $registro)
                    <tr>
                        <td>{{ $registro->nombre }}</td>
                        <td>{{ $registro->cod_alumno }}</td>
                        <td>{{ $registro->programa_academico }}</td>
                        <td>{{ $registro->numero_radicado }}</td>
                        <td>{{ $registro->numero_radicado_salida }}</td>
                        <td>{{ $registro->cursos }}</td>
                        <td>{{ ucfirst($registro->estado) }}</td>
                        <td>
                            {{ $registro->fecha_revision ? $registro->fecha_revision->format('d-m-Y H:i:s') : 'Fecha no disponible' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
