<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTicketIdNullableInHistorial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historial', function (Blueprint $table) {
            // Hacer ticket_id nullable
            $table->unsignedBigInteger('ticket_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial', function (Blueprint $table) {
            // Revertir el cambio, haciéndolo NOT NULL de nuevo
            $table->unsignedBigInteger('ticket_id')->nullable(false)->change();
        });
    }
}
