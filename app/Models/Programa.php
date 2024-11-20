<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_programa',
        'nombre_programa',
        'anio_pensum',
        'tipo_programa',
        'tipo_grado',
        'facultad',
        'coordinador'
    ];

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'facultad');
    }
}
