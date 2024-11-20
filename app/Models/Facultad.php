<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    use HasFactory;

    // Si la tabla no sigue la convención de Laravel (plural), define el nombre de la tabla
    protected $table = 'facultades'; // Asegúrate de que la tabla se llama 'facultades'

    // Define aquí los campos que son asignables
    protected $fillable = ['codigo_facultad', 'nombre_facultad', 'decano_facultad'];
    // En el modelo Facultad


    public function programas()
    {
        return $this->hasMany(Programa::class, 'facultad');
    }
}
