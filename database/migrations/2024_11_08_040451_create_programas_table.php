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
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_programa');
            $table->string('nombre_programa');
            $table->year('anio_pensum');
            $table->string('tipo_programa');
            $table->string('tipo_grado');
            $table->unsignedBigInteger('facultad');
            $table->unsignedBigInteger('coordinador')->nullable();
            $table->timestamps();

            // Clave foránea para la relación con la tabla facultades
            $table->foreign('facultad')->references('id')->on('facultades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
