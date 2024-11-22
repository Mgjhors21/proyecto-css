<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->integer('numero_radicado_salida');
            $table->string('nombre'); // Cambia a `estudiante_nombre` si es necesario
            $table->string('apellido')->nullable(); // Cambia a `estudiante_apellido` si es necesario
            $table->string('cod_alumno');
            $table->string('programa_academico');
            $table->string('numero_radicado');
            $table->date('fecha_revision');
            $table->text('cursos'); // JSON o serialización de cursos
            $table->integer('total_horas');
            $table->string('estado');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('historial', function (Blueprint $table) {
            // Revertir el cambio a INTEGER si es necesario
            $table->integer('numero_radicado')->change();
        });
    }
};
