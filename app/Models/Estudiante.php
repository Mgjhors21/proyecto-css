<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory; // Asegúrate de incluir esto para usar las fábricas de modelos

    // Especifica la tabla asociada si es diferente de la convención
    protected $table = 'estudiantes';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',            // Agrega el campo user_id para asociar con el modelo User
        'cod_alumno',        // Ajustado para que coincida con el campo en la migración
        'documento',         // Campo Documento
        'name',             // Campo de nombre
        'last_name',         // Campo de apellido
        'semestre',          // Campo Semestre
        'telefonos',         // Campo Telefonos
        'email',             // Campo Email
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class); // Define la relación inversa
    }

    // Relación con RegistroActividad


    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }
}
