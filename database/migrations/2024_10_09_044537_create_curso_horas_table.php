<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('curso_horas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curso_id'); // Relación con la tabla 'cursos'
            $table->integer('horas')->unsigned();
            $table->year('año'); // Campo para el año
            $table->timestamps();

            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('curso_horas');
    }
};
