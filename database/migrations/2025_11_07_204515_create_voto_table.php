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
        Schema::create('Voto', function (Blueprint $table) {
            $table->increments('idVoto');
            $table->unsignedInteger('idCandidato');
            $table->unsignedInteger('idElecciones');
            $table->dateTime('fechaVoto');
            
            $table->foreign('idCandidato')->references('idCandidato')->on('Candidato');
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Voto');
    }
};
