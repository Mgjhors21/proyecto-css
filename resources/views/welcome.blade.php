@extends('template')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <div class="card">
        <!-- Título e introducción -->
        <div class="card-header">
            <h1>Bienvenidos a la Plataforma de Certificación</h1>
        </div>

        <div>
            <!-- Introducción -->
            <p class="mt-4 intro-text">
                Bienvenido a la plataforma de gestión académica de nuestra universidad. Aquí podrás acceder a una amplia
                gama de herramientas diseñadas para facilitar los procesos académicos, desde la gestión de estudiantes y expedientes
                hasta la administración de solicitudes. Nuestra plataforma está comprometida en ofrecer soluciones eficientes y
                accesibles para la comunidad universitaria, permitiendo un entorno organizado y de soporte continuo.
            </p>
        </div>

        @if (Auth::check() && (Auth::user()->hasRole('administrador') || Auth::user()->hasRole('coordinador') || Auth::user()->hasRole('vicerrectoría')))
            <div class="d-flex flex-wrap justify-content-center">
                <!-- Tarjeta 1 -->
                <div class="card m-2" style="width: 18rem;">
                    <div class="text-center mt-3">
                        <i class="bi bi-clock-history" style="font-size: 3rem; color: #007bff;"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Añadir Horas</h5>
                        <p class="card-text">Gestiona las horas de cursos de manera eficiente desde esta sección.</p>
                        <a href="{{ route('horas.cursos') }}" class="btn btn-primary">Ir a Horas</a>
                    </div>
                </div>

                <!-- Tarjeta 2 -->
                <div class="card m-2" style="width: 18rem;">
                    <div class="text-center mt-3">
                        <i class="bi bi-building" style="font-size: 3rem; color: #007bff;"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Añadir Facultad</h5>
                        <p class="card-text">Administra las facultades disponibles en nuestra universidad.</p>
                        <a href="{{ route('facultades.index') }}" class="btn btn-primary">Ir a Facultades</a>
                    </div>
                </div>

                <!-- Tarjeta 3 -->
                <div class="card m-2" style="width: 18rem;">
                    <div class="text-center mt-3">
                        <i class="bi bi-bank" style="font-size: 3rem; color: #007bff;"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Añadir Institución</h5>
                        <p class="card-text">Registra nuevas instituciones asociadas con la universidad.</p>
                        <a href="{{ route('institucion.create') }}" class="btn btn-primary">Ir a Instituciones</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
