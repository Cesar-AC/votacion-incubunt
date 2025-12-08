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
        Schema::create('Candidato', function (Blueprint $table) {
            $table->increments('idCandidato');
            $table->unsignedInteger('idPartido');
            $table->unsignedInteger('idCargo');
            $table->unsignedInteger('idUsuario');
            
            $table->foreign('idPartido')->references('idPartido')->on('Partido');
            $table->foreign('idCargo')->references('idCargo')->on('Cargo');
            $table->foreign('idUsuario')->references('idUser')->on('User');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Candidato');
    }
};
