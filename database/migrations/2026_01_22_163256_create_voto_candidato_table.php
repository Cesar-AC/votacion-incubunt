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
        Schema::create('VotoCandidato', function (Blueprint $table) {
            $table->increments('idVotoCandidato');
            $table->unsignedInteger('idCandidato');
            $table->unsignedInteger('idElecciones');
            $table->unsignedInteger('idTipoVoto');

            $table->foreign('idCandidato')->references('idCandidato')->on('Candidato');
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
            $table->foreign('idTipoVoto')->references('idTipoVoto')->on('TipoVoto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('VotoCandidato');
    }
};
