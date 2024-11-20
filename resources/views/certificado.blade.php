@extends('template')

@section('titulo', 'Solicitud de Certificado de Deportes')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">

    <div class="form-container">
        <h1>Solicitud de Certificado de Deportes</h1>
        <p>Estimado Comité de Certificación de Deportes,</p>
        <p>Por la presente, solicito la emisión del siguiente certificado de deportes. A continuación, se detallan los datos
            del solicitante y del deporte:</p>

        <form>
            <div class="input-group">
                <label for="sport-name">Nombre del Deporte:</label>
                <input id="sport-name" name="sport-name" type="text" placeholder="Nombre del deporte" />
                <i class="fas fa-basketball-ball"></i>
            </div>
            <div class="input-group">
                <label for="sport-level">Nivel de Participación:</label>
                <input id="sport-level" name="sport-level" type="text" placeholder="Nivel de participación" />
                <i class="fas fa-medal"></i>
            </div>
            <div class="input-group">
                <label for="sport-achievements">Logros:</label>
                <input id="sport-achievements" name="sport-achievements" type="text" placeholder="Logros obtenidos" />
                <i class="fas fa-trophy"></i>
            </div>
            <div class="input-group">
                <label for="sport-period">Periodo de Participación:</label>
                <input id="sport-period" name="sport-period" type="text" placeholder="Periodo de participación" />
                <i class="fas fa-calendar-alt"></i>
            </div>

            <h2>Datos del Solicitante</h2>
            <div class="input-group">
                <label for="applicant-name">Nombre Completo:</label>
                <input id="applicant-name" name="applicant-name" type="text" placeholder="Nombre completo" />
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <label for="applicant-email">Correo Electrónico:</label>
                <input id="applicant-email" name="applicant-email" type="email" placeholder="Correo electrónico" />
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-group">
                <label for="applicant-phone">Teléfono:</label>
                <input id="applicant-phone" name="applicant-phone" type="tel" placeholder="Número de teléfono" />
                <i class="fas fa-phone"></i>
            </div>

            <p>Agradezco de antemano su atención a esta solicitud. Quedo atento a cualquier información adicional que pueda
                requerir.</p>

            <input type="submit" value="Enviar Solicitud" />
            <!-- Botón para regresar al welcome -->
            <a href="{{ url('/welcome') }}" class="button-return">Regresar al Inicio</a>
        </form>
    </div>
@endsection
