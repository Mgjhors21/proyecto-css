<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\CursoHora;
use App\Models\Estudiante;
use App\Models\Institucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CursoController extends Controller
{


    public function mostrarHoras()
    {
        // Lista de categorías que queremos asignar horas
        $categorias = ['curso_seminarios', 'curso_extension'];

        // Cargar las horas configuradas previamente (si es necesario)
        $horasCategorias = CursoHora::whereIn('categoria', $categorias)->get(); // Cambié el filtro a 'categoria'

        return view('users.cursos.horas_cursos', compact('categorias', 'horasCategorias'));
    }


    // Método para guardar las horas de un curso
    public function guardarHoras(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'categoria' => 'required|in:curso_seminarios,curso_extension',  // Validar la categoría
            'horas_minimas' => 'required|integer|min:1',  // Validar las horas
            'año' => 'required|integer|min:1900|max:' . date('Y'),  // Validar el año
        ]);

        // Asignar las horas a la categoría
        CursoHora::updateOrCreate(
            ['categoria' => $request->categoria, 'año' => $request->año],  // Usamos 'categoria' y 'año' como claves únicas
            ['horas_minimas' => $request->horas_minimas]  // Guardamos las horas mínimas
        );

        // Redirigir de vuelta con mensaje de éxito
        return redirect()->back()->with('success', 'Horas del curso guardadas correctamente.');
    }


    // Método para eliminar las horas de un curso
    public function eliminarHoras($id)
    {
        $horasCurso = CursoHora::findOrFail($id);  // Buscar la hora del curso por ID
        $horasCurso->delete();  // Eliminar la configuración

        return redirect()->route('users.cursos.horas_cursos')->with('success', 'Horas del curso eliminadas correctamente.');
    }



    public function mostrarFormulario($categoria)
    {
        // Obtener las instituciones disponibles, si es necesario
        $instituciones = Institucion::all();

        return view('solicitud.registrar_curso', compact('categoria', 'instituciones'));
    }

    // Obtiene información de los cursos del usuario autenticado
    public function obtenerCursosUsuario()
    {
        if (!Auth::check()) {
            return [];
        }

        try {
            // Obtener el ID del estudiante asociado al usuario
            $estudiante = Estudiante::where('user_id', Auth::id())->first();

            if (!$estudiante) {
                Log::warning('No se encontró estudiante para el usuario: ' . Auth::id());
                return [];
            }

            // Obtener los cursos usando el estudiante_id
            $cursos = Curso::where('estudiante_id', $estudiante->id)
                ->select([
                    'id',
                    'tipo',
                    'lugar_certificado',
                    'institucion',
                    'archivo',
                    'estado',
                    'categoria',
                    'created_at',
                    'horas_cursos',  // Asegúrate de incluir 'horas_cursos'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($cursos->isEmpty()) {
                Log::info('No se encontraron cursos para el estudiante: ' . $estudiante->id);
                return [];
            }

            // Formatear los cursos con la información correcta
            return $cursos->map(function ($curso) {
                return [
                    'id' => $curso->id,
                    'tipo' => $curso->tipo, // Usar el campo tipo directamente
                    'lugar' => $curso->lugar_certificado ?? 'No especificado',
                    'horas' => $curso->horas_cursos ?? 'No especificadas',  // Asegurarse de devolver 'horas_cursos'
                    'institucion' => $curso->institucion ?? 'No especificada',
                    'estado' => strtolower($curso->estado), // Convertir estado a minúsculas para el badge
                    'fecha' => $curso->created_at->format('d/m/Y'),
                    'archivo' => $curso->archivo
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error al obtener cursos: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }


    // Crear un nuevo curso
    public function crear($categoria)
    {
        if (!in_array($categoria, ['curso_seminarios', 'curso_extension'])) {
            return redirect()->back()->withErrors('Categoría no válida.');
        }

        $instituciones = Institucion::all();
        return view('solicitud.registrar_curso', compact('instituciones', 'categoria'));
    }

    // Guardar un curso
    public function guardar(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar el tipo de curso desde el formulario
            $tipoCurso = $request->input('tipo_curso');

            if (!in_array($tipoCurso, ['seminario', 'extension'])) {
                return response()->json(['success' => false, 'message' => 'Tipo de curso no válido'], 400);
            }

            // Mapear 'tipo_curso' a 'categoria'
            $categoria = $tipoCurso === 'seminario' ? 'curso_seminarios' : 'curso_extension';

            // Validar otros campos, incluyendo 'horas'
            $validatedData = $request->validate([
                'lugar_certificado' => 'required|string|max:255',
                'horas' => 'required|integer|min:1', // Validar que horas sea un número entero mayor o igual a 1
                'institucion' => 'nullable',
                'otra_institucion' => 'nullable|string|max:255',
                'archivo' => 'required|file|mimes:pdf|max:2048',
            ]);

            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
            }

            // Encontrar al estudiante asociado
            $estudiante = Estudiante::where('user_id', Auth::id())->firstOrFail();

            // Determinar el nombre de la institución
            $institucionNombre = $request->institucion === 'Otro'
                ? $request->otra_institucion
                : optional(Institucion::find($request->institucion))->nombre;

            // Guardar el archivo
            $archivoPath = null;
            if ($request->hasFile('archivo')) {
                $archivoPath = $request->file('archivo')->store('certificados', 'public');
            }

            // Crear el curso con la información adicional de 'horas_cursos'
            $curso = Curso::create([
                'user_id' => Auth::id(),
                'estudiante_id' => $estudiante->id,
                'tipo' => ucfirst($tipoCurso),
                'lugar_certificado' => $request->lugar_certificado,
                'institucion' => $institucionNombre,
                'archivo' => $archivoPath,
                'estado' => 'Aceptado', // Estado predeterminado, puedes ajustarlo según sea necesario
                'categoria' => $categoria, // 'curso_seminarios' o 'curso_extension'
                'horas_cursos' => $request->horas, // Guardar las horas en la base de datos
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Curso registrado correctamente',
                'redirect' => '/principal'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar curso: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el curso: ' . $e->getMessage()
            ], 500);
        }
    }



    // Eliminar un curso
    public function eliminar($id)
    {
        $estudiante = Estudiante::where('user_id', Auth::id())->first();

        if (!$estudiante) {
            return redirect()->back()->withErrors('No se encontró un estudiante asociado a este usuario.');
        }

        $curso = Curso::where('id', $id)->where('estudiante_id', $estudiante->id)->first();

        if ($curso) {
            if ($curso->archivo) {
                Storage::disk('public')->delete($curso->archivo);
            }
            $curso->delete();
            return redirect()->back()->with('success', 'Curso eliminado correctamente.');
        }

        return redirect()->back()->withErrors('Curso no encontrado o no tienes permiso para eliminarlo.');
    }
}
