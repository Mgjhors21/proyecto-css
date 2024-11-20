<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'estudiante_id',
        'tipo',
        'lugar_certificado',
        'institucion',
        'archivo',
        'estado',
        'categoria',
    ];

    public function horas()
    {
        return $this->hasMany(CursoHora::class);
    }

    public function estudiante()
{
    return $this->belongsTo(Estudiante::class, 'estudiante_id');
}
}
