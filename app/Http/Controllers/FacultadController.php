<?php

namespace App\Http\Controllers;

use App\Models\Facultad;
use Illuminate\Http\Request;
use App\Models\Programa;

class FacultadController extends Controller
{

    public function create()
    {
        return view('users.facultades.create');
    }

    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([
            'codigo_facultad' => 'required|unique:facultades',
            'nombre_facultad' => 'required',
        ]);

        // Eliminar el campo 'id' del array de datos antes de la inserción
        $data = $request->except('id');

        // Crear la facultad sin el campo 'id'
        Facultad::create($data);

        return redirect()->route('facultades.index')->with('success', 'Facultad creada exitosamente');
    }

    // FacultadController.php
    public function obtenerProgramas($id)
    {
        $programas = Programa::where('facultad', $id)->get(); // Cambiar a 'facultad' si es el nombre correcto del campo
        return response()->json([
            'programas' => $programas
        ]);
    }

    public function show($id)
    {
        $facultad = Facultad::findOrFail($id);
        $programas = $facultad->programas;  // Esto debería devolver todos los programas asociados a la facultad.
        return view('users.facultades.show', compact('facultad', 'programas'));
    }

    public function index()
    {
        $facultades = Facultad::all();
        return view('users.facultades.index', compact('facultades'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo_facultad' => 'required',
            'nombre_facultad' => 'required',
        ]);

        $facultad = Facultad::findOrFail($id);
        $facultad->update($request->all());

        return redirect()->route('facultades.index')->with('success', 'Facultad actualizada exitosamente');
    }

    public function destroy($id)
    {
        $facultad = Facultad::findOrFail($id);
        $facultad->delete();

        return redirect()->route('facultades.index')->with('success', 'Facultad eliminada exitosamente');
    }
}
