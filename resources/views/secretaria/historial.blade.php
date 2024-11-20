<!-- resources/views/historial/index.blade.php -->
@extends('template')

@section('titulo', 'Historial de Cartas Enviadas')

@section('contenido')

    <link rel="stylesheet" href="{{ asset('css/historial.css') }}">

    <div class="card">
        <div class="card-header">
        <h1>Historial de Cartas Enviadas</h1>
    </div>
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
                        <td>{{ $registro->programa_academico}}</td>
                        <td>{{ $registro->numero_radicado}}</td>
                        <td>{{ $registro->numero_radicado_salida }}</td>
                        <td>{{ $registro->cursos }}</td>
                        <td>{{ ucfirst($registro->estado) }}</td>
                        <td>
                            {{-- Verifica si la fecha es null antes de formatearla --}}
                            {{ $registro->fecha_envio ? $registro->fecha_envio->format('d-m-Y H:i:s') : 'Fecha no disponible' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
