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
            $table->increments('idCargo');
            $table->string('cargo', 30);
            $table->unsignedInteger('idArea');
            
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
