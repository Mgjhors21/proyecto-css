<?php

namespace App\Imports;

use App\Models\Estudiante;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class EstudiantesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Limpia y separa el nombre y apellido
        $nombreCompleto = $this->cleanText($row['nombres']);
        $nombreArray = explode(' ', $nombreCompleto);
        $name = array_shift($nombreArray); // Primer elemento como nombre
        $last_name = implode(' ', $nombreArray); // Resto como apellidos

        // Limpiar el email y quitar la coma al final si existe
        $email = rtrim(filter_var(trim($this->cleanText($row['email'])), FILTER_SANITIZE_EMAIL), ',');

        // Limpiar el teléfono eliminando cualquier símbolo no numérico y posibles espacios
        $telefonos = preg_replace('/^\D+|\D/', '', $row['telefonos'] ?? '');

        // Asignar el user_id del usuario autenticado, si es necesario.
        return new Estudiante([
            'user_id'    => Auth::id(), // Esto puede ser ajustado si no es necesario
            'cod_alumno' => $this->cleanText($row['cod_alumno']),
            'documento'  => $this->cleanText($row['documento']),
            'name'       => $name,
            'last_name'  => $last_name,
            'semestre'   => $this->cleanText($row['semestre']),
            'telefonos'  => $telefonos,
            'email'      => $email,
        ]);
    }

    private function cleanText($text)
    {
        $text = preg_replace('/[,\-]/', '', $text); // Elimina comas y guiones
        return preg_replace('/\s+/', ' ', trim($text)); // Elimina espacios extra
    }
}
