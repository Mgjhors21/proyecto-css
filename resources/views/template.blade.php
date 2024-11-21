@extends('layouts.app')

@section('content')
    <div class="body-template">
        <div class="template">
            <!-- Botón para activar el offcanvas -->
            <button class="btn btn-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions"
                aria-controls="offcanvasWithBothOptions">
                Menú
            </button>

            <!-- Offcanvas -->
            <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
                aria-labelledby="offcanvasWithBothOptionsLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Menú</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul id="certificates-list">
                        @if (Auth::check())
                            @if (Auth::user()->hasRole('administrador'))
                                <li><a href="{{ route('welcome') }}">Panel Administrador</a></li>
                                <li><a href="{{ route('gestion.user') }}">Gestión de usuarios</a></li>
                                <li><a href="{{ route('principal.form') }}">Formulario de Solicitud</a></li>
                                <li><a href="{{ route('tickets.index') }}">Visualizar Tickets</a></li>
                                <li><a href="{{ route('solicitudes_lista') }}">Solicitudes</a></li>
                                <li><a href="{{ route('solicitudes.carta') }}">Respuesta Solicitud</a></li>
                                <li><a href="{{ route('historial') }}">Historial</a></li>
                            @elseif(Auth::user()->hasRole('coordinador'))
                                <li><a href="{{ route('welcome') }}">Panel Administrador</a></li>
                                <li><a href="{{ route('solicitudes_lista') }}">Solicitudes</a></li>
                            @elseif(Auth::user()->hasRole('estudiante'))
                                <li><a href="{{ route('principal.form') }}">Formulario Principal</a></li>
                                <li><a href="{{ route('tickets.index') }}">Visualizar Tickets</a></li>
                            @elseif (Auth::user()->hasRole('secretaria'))
                                <li><a href="{{ route('welcome') }}">Panel Administrador</a></li>
                                <li><a href="{{ route('vicerrectora.listaRadicado') }}">Lista de Radicados</a></li>
                                <li><a href="{{ route('solicitudes.carta') }}">Respuesta Solicitud</a></li>
                            @elseif (Auth::user()->hasRole('vicerrectoría'))
                                <li><a href="{{ route('welcome') }}">Panel Administrador</a></li>
                                <li><a href="{{ route('aprobar.solicitud') }}">Aprobar Solicitud</a></li>
                            @endif
                        @endif
                    </ul>
                    <hr>
                    <ul>
                        <li><a href="https://www.uniautonoma.edu.co" target="_blank">www.uniautonoma.edu.co</a></li>
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                Cerrar sesión desde Gmail
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>

                    </ul>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="content">
                @yield('contenido')
            </div>
        </div>
        <link rel="stylesheet" href="{{ asset('css/template.css') }}">
    </div>
@endsection
