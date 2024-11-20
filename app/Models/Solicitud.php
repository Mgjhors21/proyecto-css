<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'nombre',
        'cedula',
        'correo',
        'celular',
        'programa_academico',
        'codigo',
        'fecha_del_radicado',
        'estudiante_id', // Asegúrate de que el campo estudiante_id esté rellenable
    ];

    // Relación con el modelo Estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    // Relación con el modelo Certificado

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}
