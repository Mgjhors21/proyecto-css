<?php


namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCurso;
use App\Models\Curso;  // Este es el modelo que utilizarás ahora
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreated;

class TicketController extends Controller
{
    // Principal, No se Elimina
    public function crearTicket(Request $request)
    {
        try {
            DB::beginTransaction();

            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                return redirect()->back()->with('error', 'Usuario no autenticado');
            }

            $user = Auth::user();

            // Verificar si el usuario tiene un estudiante asociado
            if (!$user->estudiante) {
                return redirect()->back()->with('error', 'Usuario no tiene un estudiante asociado');
            }

            // Calcular las horas ya registradas en tickets anteriores de este usuario específico
            $horasTickets = DB::table('ticket_curso')
                ->join('tickets', 'ticket_curso.ticket_id', '=', 'tickets.id')
                ->where('tickets.user_id', $user->estudiante->id)
                ->sum('ticket_curso.curso_horas');

            // Validar si ya existen 40 horas en tickets anteriores de este usuario
            if ($horasTickets >= 40) {
                return redirect()->back()->with('error', 'No se puede crear otro ticket. Ya tienes 40 horas de cursos registradas.');
            }

            // Obtener los cursos actuales del estudiante
            $cursos = Curso::where('estudiante_id', $user->estudiante->id)->get();

            // Determinar el tipo de curso basado en los cursos (asumiendo todos son del mismo tipo)
            $tipoCurso = $cursos->first() ? strtolower($cursos->first()->tipo) : null;
            if (!in_array($tipoCurso, ['extension', 'seminario'])) {
                return redirect()->back()->with('error', 'Tipo de curso no válido');
            }

            // Crear el ticket con estado_ticket en "pendiente"
            $ticket = Ticket::create([
                'user_id' => $user->estudiante->id,
                'estado_ticket' => 'pendiente',
                'tipo_curso' => $tipoCurso,
                'numero_radicado_salida' => $request->input('numero_radicado_salida'),
            ]);

            // Almacenar los cursos en la tabla ticket_curso
            foreach ($cursos as $curso) {
                $archivoCursoPath = $this->clonarArchivo($curso->archivo, 'curso', $curso->id);

                TicketCurso::create([
                    'ticket_id' => $ticket->id,
                    'curso_nombre' => $curso->tipo,
                    'curso_horas' => $curso->horas()->sum('horas'), // Asumiendo relación con CursoHora
                    'curso_fecha' => $curso->created_at,
                    'estado_curso' => 'pendiente',
                    // Asignar a curso_seminario_id o curso_extension_id según el tipo
                    ($tipoCurso === 'seminario' ? 'curso_seminario_id' : 'curso_extension_id') => $curso->id,
                    // Asignar el archivo al campo correcto
                    ($tipoCurso === 'seminario' ? 'archivo_seminario' : 'archivo_extension') => $archivoCursoPath,
                    'codigo_estudiante' => $user->estudiante->codigo // Asumiendo que existe este campo
                ]);
            }

            DB::commit();

            // Enviar correo de confirmación
            $userEmail = $user->email;
            $data = [
                'asunto' => 'Horas de Curso',
                'descripcion' => 'Cordial saludo. Me dirijo a usted para solicitar la validación de las horas de curso. Adjunto archivo PDF. Agradezco su atención y quedo atento a sus comentarios.',
                'remitente' => $userEmail,
            ];

            Mail::to('cecd.soporte@gmail.com')->send(new TicketCreated($data));

            return redirect()->back()->with('success', 'Ticket creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear ticket: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el ticket: ' . $e->getMessage());
        }
    }

    /**
     * Clona el archivo del curso si existe
     */
    private function clonarArchivo($archivo, $tipo, $cursoId)
    {
        if (!$archivo) return null;

        $originalPath = storage_path('app/public/' . $archivo);
        if (!file_exists($originalPath)) return null;

        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $newFileName = "uploads/tickets/{$tipo}_{$cursoId}_" . time() . '.' . $extension;
        $newPath = storage_path('app/public/' . $newFileName);

        if (!file_exists(dirname($newPath))) {
            mkdir(dirname($newPath), 0755, true);
        }

        if (copy($originalPath, $newPath)) {
            return $newFileName;
        }

        return null;
    }

    // Eliminar curso, no se elimina
    public function eliminarCurso($id)
    {
        try {
            DB::beginTransaction();

            // Verificar que el usuario está autenticado
            if (!Auth::check()) {
                return redirect()->back()->with('error', 'Usuario no autenticado');
            }

            // Buscar el curso en la tabla de Cursos
            $curso = Curso::find($id);

            // Verificar si el curso fue encontrado
            if (!$curso) {
                return redirect()->back()->with('error', 'Curso no encontrado.');
            }

            // Verificar que el curso pertenece al usuario actual
            if ($curso->estudiante_id !== Auth::user()->estudiante->id) {
                return redirect()->back()->with('error', 'No tienes permiso para eliminar este curso.');
            }

            // Eliminar el curso
            $curso->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Curso eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar curso: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el curso: ' . $e->getMessage());
        }
    }




    // Principal, No se Elimina
    public function index()
    {
        // Obtener todos los tickets, agrupados por estudiante
        $solicitudesPorEstudiante = Ticket::with('ticketCursos') // Cambiado de 'curso' a 'ticketCursos'
            ->get()
            ->groupBy(function ($ticket) {
                return $ticket->user_id; // Agrupa por el ID del usuario (estudiante)
            });

        // Cambiamos la ruta de la vista a la correcta
        return view('solicitud.index', compact('solicitudesPorEstudiante'));
    }

    public function detallesCurso($id)
    {
        // Obtener el ticket por ID
        $ticket = Ticket::with('ticketCursos')->findOrFail($id);

        // Retornar la vista con los detalles del ticket
        return view('solicitud.detalles_curso', compact('ticket'));
    }

    public function solicitudes()
    {
        // Obtener tickets aprobados y rechazados con la relación estudiante
        $solicitudesAprobadas = Ticket::with('estudiante')
            ->where('estado_ticket', 'aprobado')
            ->get();

        $solicitudesRechazadas = Ticket::with('estudiante')
            ->where('estado_ticket', 'rechazado')
            ->get();

        // Pasar ambas variables a la vista
        return view('secretaria.solicitudes_aprobadas', compact('solicitudesAprobadas', 'solicitudesRechazadas'));
    }


    public function viewCarta(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        return redirect()->route('carta.show', ['id' => $ticketId]);
    }

    public function updateRadicadoSalida(Request $request, $id)
    {
        // Validar la entrada para asegurar que el número de radicado de salida esté presente
        $request->validate([
            'numero_radicado_salida' => 'required|string|max:255',
        ]);

        // Buscar el ticket correspondiente usando el ID
        $ticket = Ticket::findOrFail($id);

        // Actualizar el número de radicado de salida
        $ticket->numero_radicado_salida = $request->input('numero_radicado_salida');
        $ticket->save();

        // Redirigir de nuevo a la vista con el ticket actualizado
        return redirect()->route('solicitudes.carta', ['id' => $ticket->id])->with('success', 'Número de radicado de salida actualizado correctamente.');
    }
}
