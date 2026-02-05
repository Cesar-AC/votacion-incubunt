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
        Schema::create('Partido', function (Blueprint $table) {
            $table->increments('idPartido');
            $table->string('partido', 255);
            $table->text('urlPartido');
            $table->text('descripcion');
            $table->unsignedInteger('foto_idArchivo')->nullable();

            $table->foreign('foto_idArchivo')
                ->references('idArchivo')
                ->on('Archivo')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Partido');
    }
};
