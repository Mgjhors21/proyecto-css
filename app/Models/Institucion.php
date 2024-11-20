<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla
    protected $table = 'instituciones';

    protected $fillable = ['nombre'];
}
