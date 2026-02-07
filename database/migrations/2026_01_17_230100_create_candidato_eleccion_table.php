<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Thiagoprz\CompositeKey\HasCompositeKey;

return new class extends Migration
{
    use HasCompositeKey;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('CandidatoEleccion', function (Blueprint $table) {
            $table->unsignedInteger('idCandidato');
            $table->unsignedInteger('idElecciones');
            $table->unsignedInteger('idPartido')->nullable();
            $table->unsignedInteger('idCargo');

            $table->primary(['idCandidato', 'idElecciones']);

            $table->foreign('idCandidato')->references('idCandidato')->on('Candidato');
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
            $table->foreign('idPartido')->references('idPartido')->on('Partido');
            $table->foreign('idCargo')->references('idCargo')->on('Cargo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CandidatoEleccion');
    }
};
