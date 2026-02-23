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
        Schema::create('Logs', function (Blueprint $table) {
            $table->increments('idLog');
            $table->unsignedInteger('idCategoriaLog');
            $table->unsignedInteger('idNivelLog');
            $table->unsignedInteger('idUsuario')->nullable();
            $table->dateTime('fecha');
            $table->text('descripcion');

            $table->foreign('idCategoriaLog')->references('idCategoriaLog')->on('CategoriaLog');
            $table->foreign('idNivelLog')->references('idNivelLog')->on('NivelLog');
            $table->foreign('idUsuario')->references('idUser')->on('User');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Logs');
    }
};
