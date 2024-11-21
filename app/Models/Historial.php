<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    // Define el nombre de la tabla en la base de datos (opcional si se sigue la convención)
    protected $table = 'historial';

    // Define los campos que se pueden asignar en masa
    protected $fillable = [
        'ticket_id',
        'numero_radicado_salida',
        'nombre',
        'apellido',
        'cod_alumno',
        'programa_academico',
        'numero_radicado',
        'fecha_revision',
        'cursos',
        'total_horas',
        'estado',
    ];
    // Cast automático para campos de fecha
    protected $casts = [
        'fecha_revision' => 'datetime',
    ];
    // Aquí puedes definir las relaciones si necesitas (por ejemplo, relación con Ticket)
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
