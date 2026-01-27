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
        Schema::create('VotoPartido', function (Blueprint $table) {
            $table->increments('idVotoPartido');
            $table->unsignedInteger('idPartido');
            $table->unsignedInteger('idElecciones');
            $table->unsignedInteger('idTipoVoto');

            $table->foreign('idPartido')->references('idPartido')->on('Partido');
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
            $table->foreign('idTipoVoto')->references('idTipoVoto')->on('TipoVoto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('VotoPartido');
    }
};
