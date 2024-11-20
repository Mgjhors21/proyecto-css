@extends('template')

@section('titulo', 'Carta de Respuesta')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/carta.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/carta.js') }}"></script>

    <div class="card">

        <div class="card-container">
            <div class="card-header">
                <img src="{{ asset('images/logos autonoma_1.png') }}" alt="Logo" class="logo">
                <div style="text-align: right;">
                    <p><strong>RC-{{ $ticket->numero_radicado_salida }} </strong></p>
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
                    En respuesta a la comunicación radicada el día {{ $ticket->fecha_revision }}, con el número de
                    referencia,
                    en donde solicita la validación de las horas de seminario y cursos de extensión para optar
                    su título profesional, le informamos que su solicitud ha sido aprobada.
                </p>
            </div>
            <div class="tittle">
                <h4>1 ) Validación 96 Horas Seminario de Actualización:</h4>
            </div>
            <table class="validation-table">
                <thead>
                    <tr>
                        <th>Nombre del Seminario</th>
                        <th>Número de Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ticket->ticketCursos as $curso)
                        @if (strtolower($curso->estado_curso) === 'aceptado' && !is_null($curso->curso_seminario_id))
                            <tr>
                                <td>{{ $curso->curso_nombre }}</td>
                                <td>{{ $curso->curso_horas }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="total-label">Total:</td>
                        <td>{{ $ticket->ticketCursos->where('estado_curso', 'aceptado')->sum('curso_horas') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="tittle">
                <h4>2) Validación 40 Horas Cursos de Extensión:</h4>
            </div>
            <table class="validation-table">
                <thead>
                    <tr>
                        <th>Nombre del Curso</th>
                        <th>Número de Horas</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($ticket->ticketCursos as $curso)
                        @if (strtolower($curso->estado_curso) === 'aceptado' && !is_null($curso->curso_extension_id))
                            <tr>
                                <td>{{ $curso->curso_nombre }}</td>
                                <td>{{ $curso->curso_horas }}</td>

                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="total-label">Total:</td>
                        <td>{{ $ticket->ticketCursos->where('estado_curso', 'aceptado')->sum('curso_horas') }}</td>

                    </tr>
                </tfoot>
            </table>
            <div class="text">
                <p style="text-align: justify;">
                    En cualquier caso, la decanatura está a su disposición para orientar o aclarar cualquier duda adicional
                    que
                    le pueda surgir.Universitariamente
                </p>
                <div class="firma-container">
                    @if (session('firmaPath'))
                        <img src="{{ asset('storage/' . session('firmaPath')) }}" alt="Firma Decano" class="firma">
                    @else
                        <hr style="border: 10px solid #333333; margin: 0px 0; width: 50%; margin-left: 0;">
                    @endif
                    <p class="decano-text">Decano Facultad de Ingeniería y Ciencias Naturales</p>
                </div>
            </div>
            <!-- Pie de página -->
            <div class="footer" style="font-size: 9px; line-height: 1.2;">
                <p><strong>Lic. De Funcionamiento:</strong> 12321 de 1979. Resolución MEN Nº. 677 de 2003. Código SNIES:
                    2849</p>
                <p>Sede principal - Calle 5 N° 3 - 85 Barrio Centro.</p>
                <p>PBX: 602 8222295 - WhatsApp 314 639 54 95 - 320 575 04 64 A.A. 043 Popayán - Cauca - Colombia.</p>
                <p>www.uniautonoma.edu.co - Email: recepción@uniautonoma.edu.co</p>
            </div>
        </div>
        <div>
            <form id="cartaForm" action="{{ route('carta') }}" method="POST">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <button type="submit" class="btn-enviar">Enviar</button>
            </form>
        </div>
        <div>
            <a href="{{ route('subir.firma') }}" class="btn btn-secondary mt-3">
                <i class="fas fa-pen"></i> Subir Firma Digital
            </a>
        </div>

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
