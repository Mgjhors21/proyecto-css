@extends('template')

@section('titulo', 'Carta de Respuesta')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/carta.css') }}">

    <div class="content-inner">
        <h1 class="page-title">Carta de Respuesta</h1>

        <div class="card-container">
            <div class="card-header">
                <img src="{{ asset('images/logos autonoma_1.png') }}" alt="Logo" class="logo">
                <div style="text-align: right;">
                    <p><strong>RC-{{$ticket->numero_radicado_salida}} </strong></p>
                    <h4>Popayán, {{ now()->format('d M. Y') }}</h4>
                </div>
            </div>

            <div class="student-info">
                <p><strong>Estudiante:</strong><br>
                    {{ $ticket->estudiante->name }} {{ $ticket->estudiante->apellido }}</p>

                <p><strong>Codigo Estudiante</strong><br>
                    {{ $ticket->estudiante->cod_alumno }}

                <p><strong>Programa:</strong><br>
                    {{ $ticket->estudiante->programa_academico }}</p>

                <p>
                    <strong>Asunto:</strong> Radicado No. {{ $ticket->numero_radicado }} – Validación Horas Seminario y
                    Horas Cursos de Extensión.
                </p>
            </div>

            <div class="text">

                <p>Cordial Saludo,</p>

                <p style="text-align: justify;">
                    En respuesta a la comunicación radicada el día {{ $ticket->fecha_radicado }}, con el número de
                    referencia,
                    en donde solicita la validación de las horas de seminario y cursos de extensión para optar
                    su título profesional, le informamos que su solicitud no cumple con los requisitos establecidos para la
                    convalidación de las horas.
                </p>
            </div>

            <!-- Tabla de Validación -->
            <div class="table-section">
                <h4>Validación de Cursos</h4>
                <table class="validation-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Estado</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($ticket->ticketCursos && $ticket->ticketCursos->isNotEmpty())
                            @foreach ($ticket->ticketCursos as $curso)
                                @if ($curso->estado_curso === 'rechazado')
                                    <tr>
                                        <td>{{ $curso->curso_nombre }}</td>
                                        <td style="text-align: center;">{{ $curso->estado_curso }}</td>
                                        <td>{{ $curso->curso_descripcion }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">No hay cursos rechazados asociados a este ticket.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="text">
                <p style="text-align: justify;">
                    En cualquier caso, la decanatura está a su disposición para orientar o aclarar cualquier duda adicional
                    que
                    le pueda surgir.
                </p>

                <p>
                    Universitariamente,<br><br>
                    <br><br>
                    <br><br>
                    <!-- Ajusta el estilo según sea necesario -->
                    <hr style="border: 1px solid #333333; margin: 20px 0; width: 50%; margin-left: 0;">
                    Decano Facultad de Ingeniería y Ciencias Naturales
                </p>
            </div>

            <!-- Pie de página -->
            <div class="footer" style="font-size: 7px; line-height: 1.2;">
                <p><strong>Lic. De Funcionamiento:</strong> 12321 de 1979. Resolución MEN Nº. 677 de 2003. Código SNIES:
                    2849</p>
                <p>Sede principal - Calle 5 N° 3 - 85 Barrio Centro.</p>
                <p>PBX: 602 8222295 - WhatsApp 314 639 54 95 - 320 575 04 64 A.A. 043 Popayán - Cauca - Colombia.</p>
                <p>www.uniautonoma.edu.co - Email: recepción@uniautonoma.edu.co</p>
            </div>
        </div>

        <form id="cartaForm" action="{{ route('cartarechazo') }}" method="POST">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            <button type="submit" class="btn-enviar">Enviar</button>
        </form>


        </div>

        <script>
          document.getElementById('cartaForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir el envío del formulario por defecto

        // Mostrar el SweetAlert de confirmación
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Quieres enviar esta carta de respuesta?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, enviar el formulario
                this.submit();
            }
        });
    });

        </script>
@endsection
