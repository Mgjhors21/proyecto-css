<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCurso;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreated;

class TicketController extends Controller
{
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

            // Obtener los cursos aceptados del estudiante
            $cursos = Curso::where('estudiante_id', $user->estudiante->id)
                ->where('estado', 'Aceptado')
                ->get();

            if ($cursos->isEmpty()) {
                return redirect()->back()->with('error', 'No hay cursos aceptados para crear un ticket');
            }

            // Determinar el tipo de curso basado en la categoría
            $categoria = strtolower(trim($cursos->first()->categoria));
            $tipoCurso = match ($categoria) {
                'curso_seminarios' => 'curso_seminarios',
                'curso_extension' => 'curso_extension',
                default => null,
            };

            // Validar que el tipo de curso sea válido
            if (!$tipoCurso) {
                return redirect()->back()->with('error', 'Tipo de curso no válido para crear un ticket');
            }

            // Calcular horas totales
            $horasTotales = $cursos->sum('horas_cursos');

            // Consultar las horas mínimas requeridas
            $horasMinimas = DB::table('curso_horas')
                ->where('categoria', $tipoCurso)
                ->value('horas_minimas');

            if (!$horasMinimas) {
                return redirect()->back()->with('error', 'No se encontraron horas mínimas configuradas para este tipo de curso');
            }

            // Validar si las horas totales cumplen con las mínimas
            if ($horasTotales < $horasMinimas) {
                return redirect()->back()->with('error', 'Las horas totales de los cursos son menores a las mínimas requeridas (' . $horasMinimas . ' horas).');
            }

            // Generar número de radicado único
            $numeroRadicado = $this->generarNumeroRadicadoUnico();

            // Crear el ticket
            $ticket = Ticket::create([
                'user_id' => $user->estudiante->id,
                'estado_ticket' => 'pendiente',
                'tipo_curso' => $tipoCurso,
                'numero_radicado' => $numeroRadicado,
            ]);

            // Almacenar los cursos en ticket_curso
            foreach ($cursos as $curso) {
                $archivoCursoPath = $this->clonarArchivo($curso->archivo, 'curso', $curso->id);

                TicketCurso::create([
                    'ticket_id' => $ticket->id,
                    'curso_nombre' => $curso->tipo,
                    'curso_horas' => $curso->horas_cursos,
                    'curso_fecha' => $curso->created_at,
                    'estado_curso' => 'pendiente',
                    'curso_seminario_id' => $tipoCurso === 'curso_seminarios' ? $curso->id : null,
                    'curso_extension_id' => $tipoCurso === 'curso_extension' ? $curso->id : null,
                    'archivo_seminario' => $tipoCurso === 'curso_seminarios' ? $archivoCursoPath : null,
                    'archivo_extension' => $tipoCurso === 'curso_extension' ? $archivoCursoPath : null,
                    'codigo_estudiante' => $user->estudiante->cod_alumno,
                ]);
            }

            DB::commit();

            // Enviar correo de confirmación
            $data = [
                'asunto' => 'Horas de Curso',
                'descripcion' => 'Cordial saludo. Me dirijo a usted para solicitar la validación de las horas de curso. Adjunto archivo PDF. Agradezco su atención y quedo atento a sus comentarios.',
                'remitente' => $user->email,
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
     * Genera un número de radicado único.
     *
     * @return string
     */
    private function generarNumeroRadicadoUnico()
    {
        do {
            // Generar un número aleatorio de 6 dígitos
            $numeroRadicado = str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);

            // Verificar que no exista en las tablas `tickets` y `historial`
            $existeEnTickets = Ticket::where('numero_radicado', $numeroRadicado)->exists();
            $existeEnHistorial = DB::table('historial')->where('numero_radicado', $numeroRadicado)->exists();
        } while ($existeEnTickets || $existeEnHistorial);

        return $numeroRadicado;
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
