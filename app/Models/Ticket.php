<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'user_id',
        'estado_ticket',
        'estado_curso',
        'tipo_curso',
        'fecha_revision',
        'comentario_secretaria',
        'revisor_id',
        'comentario_revisor',
        'numero_radicado',
        'numero_radicado_salida' // Agregar este campo
    ];

    // Relación principal para los cursos del ticket
    public function ticketCursos()
    {
        return $this->hasMany(TicketCurso::class);
    }
    // Relación con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'user_id', 'id');
    }

    // Relación con el revisor
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }

    public function historial()
    {
        return $this->hasOne(Historial::class);
    }
}
