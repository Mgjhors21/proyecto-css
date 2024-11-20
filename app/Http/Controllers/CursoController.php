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

class CursoController extends Controller
{

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

        $userId = Auth::id();

        try {
            // Obtener los cursos del usuario
            $cursos = Curso::where('user_id', $userId)
                ->with('horas') // Relación con la tabla 'curso_horas'
                ->select([
                    'id',
                    'tipo',
                    'lugar_certificado',
                    'institucion',
                    'archivo',
                    'estado',
                    'categoria',
                    'created_at',
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Formatear los cursos
            $cursos = $cursos->map(function ($curso) {
                return [
                    'id' => $curso->id,
                    'tipo' => ucfirst(str_replace('_', ' ', $curso->categoria)), // Convertir formato
                    'lugar' => $curso->lugar_certificado ?? 'No especificado',
                    'institucion' => $curso->institucion ?? 'No especificada',
                    'horas' => $curso->horas->sum('horas'), // Total de horas
                    'archivo' => $curso->archivo,
                    'estado' => $curso->estado ?? 'pendiente',
                    'fecha' => $curso->created_at ? $curso->created_at->format('d/m/Y') : 'Fecha no disponible',
                ];
            });

            return $cursos;
        } catch (\Exception $e) {
            Log::error('Error al obtener cursos: ' . $e->getMessage());
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
        // Validar el tipo de curso desde el formulario
        $tipoCurso = $request->input('tipo_curso');

        if (!in_array($tipoCurso, ['seminario', 'extension'])) {
            return response()->json(['success' => false, 'message' => 'Tipo de curso no válido'], 400);
        }

        // Mapear 'tipo_curso' a 'categoria'
        $categoria = $tipoCurso === 'seminario' ? 'curso_seminarios' : 'curso_extension';

        // Validar otros campos con base en la categoría
        $validatedData = $request->validate([
            'lugar_certificado' => 'required|string|max:255',
            'horas' => 'required|integer|min:1',
            'institucion' => 'nullable',
            'otra_institucion' => 'nullable|string|max:255',
            'archivo' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Asegurarse de que el usuario está autenticado
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
        }

        // Encontrar al estudiante asociado
        $estudiante = Estudiante::where('user_id', Auth::id())->first();
        if (!$estudiante) {
            return response()->json(['success' => false, 'message' => 'Estudiante no encontrado'], 404);
        }

        try {
            // Determinar el nombre de la institución
            $institucionNombre = $request->institucion === 'Otro'
                ? $request->otra_institucion
                : Institucion::find($request->institucion)->nombre ?? null;

            // Crear el curso
            $curso = new Curso();
            $curso->user_id = Auth::id();
            $curso->estudiante_id = $estudiante->id;
            $curso->tipo = ucfirst($tipoCurso); // 'Seminario' o 'Extension'
            $curso->categoria = $categoria;
            $curso->lugar_certificado = $request->lugar_certificado;
            $curso->institucion = $institucionNombre;
            $curso->estado = 'Aceptado';

            // Almacenar el archivo
            $curso->archivo = $request->file('archivo')->store('certificados', 'public');
            $curso->save();

            // Guardar las horas del curso
            $cursoHora = new CursoHora();
            $cursoHora->curso_id = $curso->id;
            $cursoHora->horas = $request->horas;
            $cursoHora->año = date('Y'); // Por defecto, el año actual
            $cursoHora->save();

            return response()->json([
                'success' => true,
                'message' => 'Curso registrado correctamente',
                'redirect' => '/principal'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar curso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
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
