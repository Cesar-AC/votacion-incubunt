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
        Schema::create('RolUser', function (Blueprint $table) {
            $table->unsignedInteger('idRol');
            $table->unsignedInteger('idUser');
            
            $table->primary(['idRol', 'idUser']);
            
            $table->foreign('idRol')->references('idRol')->on('Rol');
            $table->foreign('idUser')->references('idUser')->on('User');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RolUser');
    }
};
