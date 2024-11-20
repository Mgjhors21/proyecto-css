<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoHora extends Model
{
    use HasFactory;

    protected $fillable = ['categoria', 'horas_minimas', 'año'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
