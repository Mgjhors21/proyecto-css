<?php

namespace App\Http\Controllers;

use App\Models\Facultad;
use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function create($facultadId)
    {
        $facultad = Facultad::find($facultadId);

        if (!$facultad) {
            // Maneja el caso en que no se encuentra la facultad
            return redirect()->route('facultades.index')->with('error', 'Facultad no encontrada');
        }

        return view('users.programas.create', compact('facultad'));
    }


    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([
            'codigo_programa' => 'required|unique:programas',
            'nombre_programa' => 'required',
            'anio_pensum' => 'required|integer',
            'tipo_programa' => 'required',
            'tipo_grado' => 'required',
            'facultad' => 'required|exists:facultades,id', // Aseguramos que el ID de la facultad exista en la tabla facultades
        ]);

        // Crear un nuevo registro en la tabla programas
        $programa = new Programa();
        $programa->codigo_programa = $request->codigo_programa;
        $programa->nombre_programa = $request->nombre_programa;
        $programa->anio_pensum = $request->anio_pensum;
        $programa->tipo_programa = $request->tipo_programa;
        $programa->tipo_grado = $request->tipo_grado;
        $programa->facultad = $request->facultad;  // Usamos 'facultad' aquí
        $programa->save();

        return redirect()->route('programas.create')->with('success', 'Programa creado exitosamente');
    }
}
