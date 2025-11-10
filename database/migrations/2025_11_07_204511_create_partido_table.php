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
            $table->integer('idPartido')->autoIncrement()->primary();
            $table->integer('idElecciones');
            $table->string('partido', 255);
            $table->text('urlPartido');
            $table->text('descripcion');
            
            $table->foreign('idElecciones')->references('idElecciones')->on('Elecciones');
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
