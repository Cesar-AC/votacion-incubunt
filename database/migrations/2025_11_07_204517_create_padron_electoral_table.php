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
        Schema::create('PadronElectoral', function (Blueprint $table) {
            $table->increments('idPadronElectoral');
            $table->unsignedInteger('idElecciones');
            $table->unsignedInteger('idParticipante');
            
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
            $table->foreign('idParticipante')->references('idParticipante')->on('Participante');
            
            $table->unique(['idElecciones', 'idParticipante']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PadronElectoral');
    }
};
