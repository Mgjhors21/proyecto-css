@extends('template')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    <div class="card ">
        <!-- Título e introducción -->
        <div class="card-header">
            <h1>Bienvenidos a la Plataforma de Certificación</h1>
        </div>

        <div>
            <!-- Introducción -->
            <p class="mt-4 intro-text">
                Bienvenido a la plataforma de gestión académica de nuestra universidad. Aquí podrás acceder a una amplia
                gama de
                herramientas diseñadas para facilitar los procesos académicos, desde la gestión de estudiantes y expedientes
                hasta la administración de solicitudes. Nuestra plataforma está comprometida en ofrecer soluciones
                eficientes y
                accesibles para la comunidad universitaria, permitiendo un entorno organizado y de soporte continuo.
            </p>
        </div>
    </div>
@endsection
