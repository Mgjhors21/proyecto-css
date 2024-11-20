<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_curso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('curso_seminario_id')->nullable();
            $table->unsignedBigInteger('curso_extension_id')->nullable();
            $table->string('curso_nombre')->nullable();
            $table->integer('curso_horas')->nullable();
            $table->date('curso_fecha')->nullable();
            $table->text('curso_descripcion')->nullable();
            $table->enum('estado_curso', ['pendiente', 'aceptado', 'rechazado'])->default('pendiente');
            $table->string('archivo_seminario')->nullable(); // Para almacenar archivo de seminario
            $table->string('archivo_extension')->nullable(); // Para almacenar archivo de extensión
            $table->string('codigo_estudiante')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_curso');
    }
};
