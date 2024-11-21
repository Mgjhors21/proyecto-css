<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\TicketCurso;
use Illuminate\Support\Facades\DB;
use App\Models\Historial;

class SecretariaController extends Controller
{

    public function actualizarEstado(Request $request, $ticketId, $estado)
    {
        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $ticket->estado_ticket = $estado;

            // Si el estado es 'aprobado' o 'rechazado', generar número de radicado de salida
            if (in_array($estado, ['aprobado', 'rechazado'])) {
                $numeroRadicadoSalida = $this->generarNumeroRadicadoUnicoSalida();

                // Asignar el número de radicado de salida al ticket
                $ticket->numero_radicado_salida = $numeroRadicadoSalida;
            }

            $ticket->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "La solicitud ha sido {$estado}a exitosamente",
                'newStatus' => $estado,
                'numero_radicado_salida' => $ticket->numero_radicado_salida ?? null
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => "Error al actualizar el estado: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera un número de radicado único para la salida.
     *
     * @return int
     */
    private function generarNumeroRadicadoUnicoSalida()
    {
        do {
            // Generar un número aleatorio de 6 dígitos
            $numeroRadicadoSalida = random_int(100000, 999999);

            // Verificar que no exista en la tabla `historial`
            $existeEnHistorial = Historial::where('numero_radicado_salida', $numeroRadicadoSalida)->exists();
        } while ($existeEnHistorial);

        return $numeroRadicadoSalida;
    }


    public function aprobarSolicitud($ticketId)
    {
        $ticket = Ticket::with('ticketCursos')->findOrFail($ticketId);

        $this->actualizarEstado(request(), $ticketId, 'aprobado');

        // Verificar que todos los cursos estén aprobados
        $pendientes = $ticket->ticketCursos()->where('estado_curso', 'pendiente')->count();
        if ($pendientes > 0) {
            return redirect()->back()->with('error', 'No puedes aprobar el ticket hasta que todos los cursos estén aprobados.');
        }

        $ticket->estado_ticket = 'aprobado';
        $ticket->save();

        return redirect()->back()->with('success', "El ticket ha sido aprobado exitosamente.");
    }

    public function rechazarSolicitud($ticketId)
    {
        $ticket = Ticket::with('ticketCursos')->findOrFail($ticketId);

        $this->actualizarEstado(request(), $ticketId, 'rechazado');


        // Verificar que todos los cursos estén rechazados
        $noRechazados = $ticket->ticketCursos()->where('estado_curso', '!=', 'rechazado')->count();
        if ($noRechazados > 0) {
            return redirect()->back()->with('error', 'No puedes rechazar el ticket hasta que todos los cursos estén rechazados.');
        }

        $ticket->estado_ticket = 'rechazado';
        $ticket->save();

        return redirect()->back()->with('error', "El ticket ha sido rechazado.");
    }

    public function rechazarCurso(Request $request, $id, $tipoCurso)
    {
        // Buscar el curso en la base de datos
        $ticketCurso = TicketCurso::find($id);

        if (!$ticketCurso) {
            return redirect()->back()->with('error', 'Curso no encontrado.');
        }

        // Validar que se proporcione una descripción del rechazo
        if (!$request->has('curso_descripcion') || empty($request->input('curso_descripcion'))) {
            return redirect()->back()->with('error', 'La descripción del rechazo es requerida.');
        }

        // Establecer el estado del curso como 'rechazado' y guardar la razón en curso_descripcion
        $ticketCurso->estado_curso = 'rechazado';
        $ticketCurso->curso_descripcion = $request->input('curso_descripcion');
        $ticketCurso->save();

        // Verificar si todos los cursos del ticket están en estado final (aprobado/rechazado)
        $ticket = $ticketCurso->ticket;

        if ($ticket) {
            $cursosPendientes = $ticket->ticketCursos()->where('estado_curso', 'pendiente')->count();

            // Si no hay cursos pendientes, evaluar el estado final del ticket
            if ($cursosPendientes === 0) {
                $todosRechazados = $ticket->ticketCursos()->where('estado_curso', '!=', 'rechazado')->count() === 0;

                if ($todosRechazados) {
                    $ticket->estado_ticket = 'rechazado';
                    $ticket->save();
                }
            }

            // Redirigir a la ruta con el ID del estudiante
            return redirect()->route('solicitud.detalles', ['id' => $ticket->user_id])
                ->with('success', 'Curso rechazado exitosamente.');
        } else {
            return redirect()->back()->with('error', 'Ticket no encontrado.');
        }
    }


    public function aprobarCurso($id)
    {
        try {
            $ticketCurso = TicketCurso::findOrFail($id);
            $ticketCurso->estado_curso = 'aceptado';
            $ticketCurso->save();

            return redirect()->back()->with('success', 'Curso aprobado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al aprobar el curso: ' . $e->getMessage());
        }
    }
    public function listarTickets()
    {
        $solicitudesPorEstudiante = Ticket::whereNotNull('numero_radicado')
            ->with('estudiante')
            ->get()
            ->groupBy('user_id');

        $primerEstudiante = null;
        if ($solicitudesPorEstudiante->isNotEmpty()) {
            $primerEstudiante = $solicitudesPorEstudiante->first()->first()->estudiante;
        }

        return view('secretaria.solicitudes_lista', compact('solicitudesPorEstudiante', 'primerEstudiante'));
    }


    // Controlador


    public function verDetalles($id)
    {
        $estudiante = Estudiante::findOrFail($id);

        $solicitudes = Ticket::where('user_id', $estudiante->id)
            ->with('ticketCursos')
            ->get();

        return view('secretaria.detalles_solicitud', compact('estudiante', 'solicitudes'));
    }

    public function Historial(Request $request)
    {

        $query = Historial::query(); // Modelo asociado a los datos de historial

        // Obtener el parámetro de búsqueda
        $search = $request->input('search');

        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                ->orWhere('cod_alumno', 'like', '%' . $search . '%')
                ->orWhere('numero_radicado', 'like', '%' . $search . '%')
                ->orWhere('numero_radicado_salida', 'like', '%' . $search . '%');
        }

        // Obtener los datos filtrados
        $historial = $query->get();
        return view('secretaria.historial', compact('historial'));
    }
}
