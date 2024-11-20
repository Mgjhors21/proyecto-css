<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('curso_horas', function (Blueprint $table) {
            $table->id();
            $table->enum('categoria', ['curso_seminarios', 'curso_extension']); // Almacena la categoría directamente
            $table->integer('horas_minimas')->unsigned(); // Número de horas
            $table->year('año'); // Año del curso
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('curso_horas');
    }
};
