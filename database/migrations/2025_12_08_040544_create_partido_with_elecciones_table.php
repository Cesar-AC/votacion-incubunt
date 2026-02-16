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
        Schema::create('PartidoEleccion', function (Blueprint $table) {
            $table->unsignedInteger('idPartido');
            $table->unsignedInteger('idElecciones');

            $table->primary(['idPartido', 'idElecciones']);

            $table->foreign('idPartido')->references('idPartido')->on('Partido');
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PartidoEleccion');
    }
};
