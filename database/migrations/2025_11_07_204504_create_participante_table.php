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
        Schema::create('Participante', function (Blueprint $table) {
            $table->increments('idParticipante');
            $table->string('nombre', 60);
            $table->string('apellidos', 60);
            $table->unsignedInteger('idUser');
            $table->unsignedInteger('idCarrera');
            $table->text('biografia');
            $table->text('experiencia');
            $table->integer('estado');
            
            $table->foreign('idUser')->references('idUser')->on('User');
            $table->foreign('idCarrera')->references('idCarrera')->on('Carrera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Participante');
    }
};
