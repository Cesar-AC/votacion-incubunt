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
        Schema::create('Elecciones', function (Blueprint $table) {
            $table->increments('idElecciones');
            $table->string('titulo', 255);
            $table->text('descripcion');
            $table->dateTime('fechaInicio');
            $table->dateTime('fechaCierre');
            $table->unsignedInteger('idEstado');
            
            $table->foreign('idEstado')->references('idEstado')->on('EstadoElecciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Elecciones');
    }
};
