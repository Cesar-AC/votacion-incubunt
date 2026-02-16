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
        Schema::create('PadronElectoral', function (Blueprint $table) {
            $table->unsignedInteger('idElecciones');
            $table->unsignedInteger('idUsuario');
            $table->dateTime('fechaVoto')->nullable();

            $table->primary(['idElecciones', 'idUsuario']);
            
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
            $table->foreign('idUsuario')->references('idUser')->on('User');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PadronElectoral');
    }
};
