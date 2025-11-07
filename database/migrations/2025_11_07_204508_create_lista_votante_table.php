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
        Schema::create('ListaVotante', function (Blueprint $table) {
            $table->integer('idListaVotante')->autoIncrement()->primary();
            $table->integer('idUser');
            $table->integer('idElecciones');
            $table->dateTime('fechaVoto');
            $table->integer('idTipoVoto');
            
            $table->foreign('idUser')->references('idUser')->on('User');
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
            $table->foreign('idTipoVoto')->references('idTipoVoto')->on('TipoVoto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ListaVotante');
    }
};
