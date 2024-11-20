<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Estudiante;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EstudiantesImport;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Models\Programa;
use App\Models\Institucion;
use App\Models\Facultad;
use Illuminate\Support\Facades\Validator;



class EstudianteController extends Controller
{

    public function showUploadForm()
    {
        $estudiantes = Estudiante::all(); // Obtener todos los estudiantes
        return view('users.upload_csv', compact('estudiantes')); // Pasar los estudiantes a la vista
    }

    // Maneja la subida y almacenamiento del archivo CSV
    public function import(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['error' => 'No estás autenticado']);
        }

        // Validar el archivo
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048', // Máximo 2MB
        ]);

        try {
            // Almacenar el archivo y obtener la ruta
            $filePath = $request->file('file')->store('uploads');

            // Importar los datos usando la clase EstudiantesImport
            Excel::import(new EstudiantesImport, $filePath);

            return redirect()->route('users.upload_csv')->with('success', 'Los datos han sido importados correctamente.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors()); // Cambiar getErrors() a errors()
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Hubo un error al importar el archivo.']);
        }
    }

    // Mostrar el formulario con los datos del usuario autenticado
    public function mostrarPrincipalForm(Request $request)
    {
        $user = Auth::user();

        if ($user && $user instanceof User) {
            // Obtener el estudiante asociado al usuario autenticado
            $estudiante = Estudiante::where('user_id', $user->id)->first();

            // Si no existe, crea un nuevo registro
            if (!$estudiante) {
                $estudiante = new Estudiante();
                $estudiante->user_id = $user->id;
                $estudiante->save();
            }

            // Obtener las facultades
            $facultad = Facultad::all();  // Aquí agregamos la consulta para las facultades

            $programas = Programa::select('nombre_programa')->get();

            // Si se seleccionó una facultad específica, obtenemos los programas de esa facultad
            $facultadSeleccionadaId = $request->input('Facultad'); // Suponiendo que pasas la facultad seleccionada por POST
            if ($facultadSeleccionadaId) {
                $programas = Programa::where('facultad', $facultadSeleccionadaId)->get();
            }


            // Pasar los datos del estudiante a la vista
            return view('solicitud.principal_form', [
                'user' => $user,
                'nombre' => old('nombre', $estudiante->name ?? $user->name),
                'last_name' => old('last_name', $estudiante->last_name ?? $user->last_name),
                'cedula' => old('cedula', $estudiante->documento ?? ''),
                'celular' => old('celular', $estudiante->telefonos ?? ''),
                'programa_academico' => old('programa_academico', $estudiante->programa_academico ?? ''),
                'correo' => old('correo', $user->email),
                'codigo_estudiante' => old('codigo_estudiante', $estudiante->cod_alumno ?? ''),
                'programas' => $programas,
                'facultad' => $facultad,  // Asegúrate de pasar la variable facultad

            ]);
        } else {

            return redirect()->route('login')->withErrors('Debes iniciar sesión para acceder a esta página.');
        }
    }

    // Mostrar el formulario sin datos (nuevo método agregado)
    public function mostrarFormulario()
    {
        return view('estudiante.formulario'); // Asegúrate de que esta vista exista
    }

    public function obtenerProgramasPorFacultad($facultadId)
    {
        // Obtener los programas de la facultad seleccionada
        $programas = Programa::where('facultad', $facultadId)->get();

        // Retornar los programas como JSON
        return response()->json($programas);
    }


    // Guardar los datos del estudiante
    public function guardarEstudiante(Request $request, $id)
    {
        // Validación de todos los campos del formulario
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'cedula' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:255',
            'programa_academico' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'codigo_estudiante' => 'nullable|string|max:255',
        ]);

        // Buscar el usuario por su id y verificar autenticación
        $user = User::findOrFail($id);
        if ($user) {
            // Buscar o crear el registro del estudiante asociado al usuario
            $estudiante = Estudiante::firstOrCreate(['user_id' => $user->id], ['user_id' => $user->id]);

            // Asignar los valores del formulario al modelo Estudiante
            $estudiante->name = $request->nombre;
            $estudiante->last_name = $request->last_name;
            $estudiante->documento = $request->cedula;
            $estudiante->telefonos = $request->celular;
            $estudiante->programa_academico = $request->programa_academico;
            $estudiante->email = $request->correo;
            $estudiante->cod_alumno = $request->codigo_estudiante;

            // Guardar los cambios en la base de datos
            if ($estudiante->save()) {
                return redirect()->route('estudiante')->with('success', 'Datos del estudiante guardados correctamente.');
            }

            return redirect()->back()->withErrors('Error al guardar los datos del estudiante.');
        }

        return redirect()->route('login')->withErrors('No se encontró el usuario.');
    }

    public function createInstitucion()
    {
        $instituciones = Institucion::all();  // Obtener todas las instituciones
        return view('users.create_institucion', compact('instituciones'));  // Muestra el formulario
    }

    public function storeInstitucion(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombre' => 'required|unique:instituciones|max:255',
        ]);

        // Crear la nueva institución
        Institucion::create([
            'nombre' => $request->nombre,
        ]);

        // Redirigir con mensaje de éxito
        return redirect()->route('institucion.create')->with('success', 'Institución registrada con éxito.');
    }


    public function destroyInstitucion($id)
    {
        // Encontrar la institución por su ID
        $institucion = Institucion::findOrFail($id);

        // Eliminar la institución
        $institucion->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('institucion.create')->with('success', 'Institución eliminada con éxito.');
    }

    public function actualizar(Request $request, $id)
    {
        $validatedData = $request->validate([
            'cod_alumno' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'documento' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:estudiantes,email,' . $id,
            'telefonos' => 'nullable|string|max:20',
            'programa_academico' => 'nullable|string|max:255',
        ]);

        $estudiante = Estudiante::findOrFail($id);
        $estudiante->update($validatedData);

        // Retornar con mensaje de éxito
        return back()->with('success', 'Estudiante actualizado correctamente.');
    }
}
