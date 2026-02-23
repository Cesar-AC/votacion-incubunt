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
        Schema::create('Archivo', function (Blueprint $table) {
            $table->increments('idArchivo');
            $table->string('disco', 100);
            $table->string('ruta', 255);
            $table->string('mime', 255);
            $table->unsignedBigInteger('tamaño');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Archivo');
    }
};
