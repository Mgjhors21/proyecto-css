<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\CursoExtension;
use App\Models\TicketCurso;
use App\Models\CursoSeminario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Historial;

class SecretariaController extends Controller
{
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
    public function actualizarEstado(Request $request, $ticketId, $estado)
    {
        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $ticket->estado_ticket = $estado;
            $ticket->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "La solicitud ha sido {$estado}a exitosamente",
                'newStatus' => $estado
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Error al actualizar el estado: " . $e->getMessage()
            ], 500);
        }
    }

    public function aprobarSolicitud($ticketId)
    {
        $this->actualizarEstado(request(), $ticketId, 'aprobado');
        return redirect()->back()
            ->with('success', "La solicitud ha sido aprobada exitosamente.");
    }

    public function rechazarSolicitud($ticketId)
    {

        $this->actualizarEstado(request(), $ticketId, 'rechazado');
        return redirect()->back()
        ->with('error', "La solicitud ha sido rechazada.");
    }

    public function rechazarCurso(Request $request, $id, $tipoCurso)
    {
        // Buscar el curso en la base de datos
        $ticketCurso = TicketCurso::find($id);

        if (!$ticketCurso) {
            return redirect()->back()->with('error', 'Curso no encontrado.');
        }

        // Establecer el estado del curso como 'rechazado'
        $ticketCurso->estado_curso = 'rechazado';

        // Guardar la razón del rechazo en curso_descripcion
        if ($request->has('curso_descripcion')) {
            $ticketCurso->curso_descripcion = $request->input('curso_descripcion');
        } else {
            return redirect()->back()->with('error', 'La descripción del rechazo es requerida.');
        }

        // Guardar los cambios en la base de datos
        $ticketCurso->save();

        // Obtener el Ticket relacionado
        $ticket = $ticketCurso->ticket;

        if ($ticket) {
            // Obtener el ID del estudiante relacionado con este ticket
            $estudianteId = $ticket->user_id;

            // Redirigir a la ruta con el ID del estudiante
            return redirect()->route('solicitud.detalles', ['id' => $estudianteId])
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

            // Verificar si todos los cursos del ticket están aprobados
            $ticket = $ticketCurso->ticket;
            $todosAprobados = $ticket->ticketCursos()
                ->where('estado_curso', '!=', 'aceptado')
                ->count() === 0;

            if ($todosAprobados) {
                $ticket->estado_vicerrectoria = 'aprobado';
                $ticket->save();
            }

            return redirect()->back()->with('success', 'Curso aprobado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al aprobar el curso: ' . $e->getMessage());
        }
    }

    public function verDetalles($id)
    {
        $estudiante = Estudiante::findOrFail($id);

        $solicitudes = Ticket::where('user_id', $estudiante->id)
            ->with('ticketCursos')
            ->get();

        return view('secretaria.detalles_solicitud', compact('estudiante', 'solicitudes'));
    }

    public function Historial()
    {
        $historial = Historial::all();
        return view('secretaria.historial', compact('historial'));
    }
}
