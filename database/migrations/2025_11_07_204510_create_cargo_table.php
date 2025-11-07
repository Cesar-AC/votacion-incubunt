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
        Schema::create('Cargo', function (Blueprint $table) {
            $table->integer('idCargo')->autoIncrement()->primary();
            $table->string('cargo', 30);
            $table->integer('idArea');
            
            $table->foreign('idArea')->references('idArea')->on('Area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Cargo');
    }
};
