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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('cod_alumno')->nullable();
            $table->string('documento')->nullable();
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('semestre')->nullable();
            $table->string('telefonos')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('programa_academico')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
