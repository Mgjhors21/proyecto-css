<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketCurso extends Model
{
    use HasFactory;

    protected $table = 'ticket_curso';

    protected $fillable = [
        'ticket_id',
        'curso_nombre',
        'curso_horas',
        'curso_fecha',
        'curso_descripcion',
        'curso_extension_id',
        'curso_seminario_id',
        'estado_curso',
        'archivo_seminario',
        'archivo_extension',
        'codigo_estudiante'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }


    public function cursoExtension()
    {
        return $this->belongsTo(CursoExtension::class, 'curso_extension_id');
    }

    public function cursoSeminario()
    {
        return $this->belongsTo(CursoSeminario::class, 'curso_seminario_id');
    }
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

}
