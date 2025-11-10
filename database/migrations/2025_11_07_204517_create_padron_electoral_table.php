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
            $table->unsignedInteger('idUser');
            $table->unsignedInteger('idEstadoParticipante');
            
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
            $table->foreign('idUser')->references('idUser')->on('User');
            $table->foreign('idEstadoParticipante')->references('idEstadoParticipante')->on('EstadoParticipante');
            
            $table->unique(['idElecciones', 'idUser']);
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
