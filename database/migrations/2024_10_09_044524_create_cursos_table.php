<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('estudiante_id');
            $table->string('tipo');
            $table->string('lugar_certificado')->nullable();
            $table->string('institucion')->nullable();
            $table->string('archivo')->nullable();
            $table->enum('estado', ['Aceptado', 'Rechazado']);
            $table->enum('categoria', ['curso_seminarios', 'curso_extension']); // Campo adicional para diferenciar
            $table->integer('horas_cursos')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('estudiante_id')->references('id')->on('estudiantes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cursos');
    }
};
