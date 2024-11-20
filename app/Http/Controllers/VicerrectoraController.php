<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class VicerrectoraController extends Controller
{

    public function listaRadicado()
    {
        // Agrupar tickets aprobados por estudiante
        $solicitudesPorEstudiante = Ticket::with(['estudiante', 'ticketCursos'])
            ->get()
            ->groupBy('estudiante_id');

        // Pasar las solicitudes agrupadas a la vista
        return view('vicerrectora.Lista_Radicado', compact('solicitudesPorEstudiante'));
    }

    public function updateRadicado(Request $request, $ticketId)
    {
        // Validar que se proporcione un número de radicado
        $request->validate([
            'numero_radicado' => 'required|string|max:255',
        ]);

        // Encontrar el ticket por su ID
        $ticket = Ticket::findOrFail($ticketId);

        // Actualizar el número de radicado
        $ticket->numero_radicado = $request->numero_radicado;
        $ticket->save();

        // Redirigir a la lista de tickets con un mensaje de éxito
        return redirect()->route('vicerrectora.listaRadicado')->with('success', 'Número de radicado actualizado exitosamente.');
    }
}
