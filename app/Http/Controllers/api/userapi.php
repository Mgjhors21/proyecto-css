<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\user;
use Illuminate\Support\Facades\Validator;

class userapi extends Controller
{
    //

    public function index()
    {

        $usuarios = user::all();

        return response()->json($usuarios, 200); //200 es el código de respuesta para OK
    }

    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => 'nullable',
            'last_name' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email|unique:users',
            'password' => 'nullable|min:8',
            'user_type' => 'nullable'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $usurios = user::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => $request->user_type
        ]);
        if (!$usurios) {
            $data = [
                'message' => 'Error al crear el usuario',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'message' => 'Usuario creado con exito',
            'status' => 201
        ];
        return response()->json($data, 201);
    }

    public function delete()
    {
        
    }
}
