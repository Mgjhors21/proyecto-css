<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Hash;
use App\Models\Rol;
use Illuminate\Validation\Rule; // Asegúrate de agregar esta líneause Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;



class UsuariosController extends Controller
{

    // Muestra la lista de usuarios con paginación
    public function usuarios(Request $request)
    {
        // Condicional para buscar por nombre o correo
        $search = $request->input('search');
        $users = User::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderBy('id')
            ->simplePaginate(5)  // Paginación de 10 usuarios por página
            ->appends(['search' => $search]);  // Esto conserva el parámetro 'search' en los enlaces de paginación

        return view('users.users', compact('users'));
    }

    // Actualiza un usuario específico
    public function updateUsuario(Request $request, $id)
    {

        $user = User::findOrFail($id);

        // Validar los campos que no son la contraseña
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@uniautonoma\.edu\.co$/',
                // Verifica si el correo es único, excluyendo el correo actual del usuario
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'required|digits_between:9,10|numeric',
            'user_type' => 'required|integer',
            'password' => 'nullable|string|confirmed|max:8', // Contraseña opcional
        ]);

        // Actualizar los datos del usuario
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->user_type = $request->user_type;

        // Solo actualiza la contraseña si se proporciona un nuevo valor
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password); // Encripta la contraseña
        }

        $user->save(); // Guarda los cambios

        // Redirigir a la vista de edición del usuario con el mensaje de éxito
        return redirect()->route('usuarios.edit', ['id' => $user->id])->with('success', 'Usuario actualizado correctamente.');
    }

    // Formulario para crear un nuevo usuario
    public function create()
    {
        return view('users.create');
    }

    // Guarda un nuevo usuario
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|digits_between:9,10|numeric', // Solo números y longitud entre 9 y 10
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@uniautonoma\.edu\.co$/',
                'unique:users,email'
            ],
            'password' => 'required|string|confirmed|max:8', // Contraseña máxima de 8 caracteres
            'user_type' => 'required|integer',
        ]);

        // Obtiene el valor máximo de id en la tabla 'users'
        $maxId = DB::table('users')->max('id');

        // Si el valor máximo de id ya es mayor que la secuencia, la secuencia se resetea
        DB::statement("SELECT setval('users_id_seq', GREATEST($maxId, nextval('users_id_seq')))");


        $user = new User($validatedData);
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return redirect()->route('usuarios.create')->with('success', 'Usuario creado correctamente');
    }

    public function edit($id)
    {
        // Encuentra el usuario por su ID
        $user = User::findOrFail($id); // esto lanzará una excepción si no se encuentra el usuario

        // Pasa el usuario a la vista de edición
        return view('users.edit', compact('user'));
    }

    // Elimina un usuario
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->back()->with('success', '¡Usuario eliminado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo eliminar el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }



    public function migrarEstudiantesAUsuarios()
    {
        $estudiantes = Estudiante::all();
        $duplicados = [];
        $contadorMigrados = 0;

        try {
            foreach ($estudiantes as $estudiante) {
                // Verificar que el estudiante tenga los campos requeridos
                if (empty($estudiante->name) || empty($estudiante->email)) {
                    continue;
                }

                // Verificar si el usuario ya existe por email
                $existingUser = User::where('email', $estudiante->email)->first();
                if ($existingUser) {
                    $duplicados[] = $estudiante->email;
                    continue;
                }

                // Crear nuevo usuario
                try {
                    $nuevoUsuario = User::create([
                        'codigo' => $estudiante->cod_alumno,
                        'name' => $estudiante->name,
                        'last_name' => $estudiante->last_name ?? '',
                        'phone' => $estudiante->telefonos ?? '',
                        'email' => $estudiante->email,
                        'password' => Hash::make($estudiante->telefonos ?? ''), // Contraseña temporal como el teléfono
                        'user_type' => 4, // Tipo estudiante
                    ]);

                    // Actualizar el estudiante con el user_id del nuevo usuario
                    $estudiante->user_id = $nuevoUsuario->id; // Asegúrate de tener esta columna en tu tabla de estudiantes
                    $estudiante->save();

                    $contadorMigrados++;
                } catch (\Exception $e) {
                    $duplicados[] = $estudiante->email . ' (Error: ' . $e->getMessage() . ')';
                }
            }

            // Mensaje de respuesta
            $mensaje = "Se migraron {$contadorMigrados} estudiantes correctamente.";
            if (count($duplicados) > 0) {
                $mensaje .= " Los siguientes correos no se pudieron migrar: " . implode(', ', $duplicados);
                return redirect()->route('users.upload_csv')->with('warning', $mensaje);
            }

            return redirect()->route('users.upload_csv')->with('success', $mensaje);
        } catch (\Exception $e) {
            return redirect()->route('users.upload_csv')->with('error', 'Error durante la migración: ' . $e->getMessage());
        }
    }
}
