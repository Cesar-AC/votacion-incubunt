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
        Schema::create('PerfilUsuario', function (Blueprint $table) {
            $table->unsignedInteger('idUser')->primary();
            $table->string('apellidoPaterno', 20);
            $table->string('apellidoMaterno', 20);
            $table->string('nombre', 20);
            $table->string('otrosNombres', 40)->nullable();
            $table->string('dni', 8)->unique();
            $table->string('telefono', 15)->nullable();
            $table->unsignedInteger('idCarrera')->nullable();
            $table->unsignedInteger('idArea');

            $table->foreign('idUser')->references('idUser')->on('User');
            $table->foreign('idCarrera')->references('idCarrera')->on('Carrera');
            $table->foreign('idArea')->references('idArea')->on('Area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PerfilUsuario');
    }
};
