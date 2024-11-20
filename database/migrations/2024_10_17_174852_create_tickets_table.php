<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // El usuario que crea el ticket (estudiante)
            $table->enum('estado_ticket', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->enum('tipo_curso', ['extension', 'seminario']); // Tipo de curso
            $table->string('numero_radicado')->nullable();
            $table->string('numero_radicado_salida')->nullable();
            $table->timestamps();
            // Relaciones
            $table->foreign('user_id')->references('id')->on('estudiantes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
