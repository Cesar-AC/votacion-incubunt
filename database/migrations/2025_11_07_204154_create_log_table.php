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
            $table->integer('idLog')->autoIncrement()->primary();
            $table->integer('idUser');
            $table->integer('idPermiso');
            $table->dateTime('fecha_log');
            $table->text('descripcion');
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
