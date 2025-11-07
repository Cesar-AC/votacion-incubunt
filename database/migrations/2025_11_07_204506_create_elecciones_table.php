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
            $table->integer('idElecciones')->autoIncrement()->primary();
            $table->string('titulo', 255);
            $table->text('descripcion');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_cierre');
            $table->integer('estado');
            
            $table->foreign('estado')->references('idEstado')->on('EstadoElecciones');
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
