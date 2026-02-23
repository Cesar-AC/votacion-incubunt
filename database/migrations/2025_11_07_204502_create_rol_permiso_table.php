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
        Schema::create('RolPermiso', function (Blueprint $table) {
            $table->unsignedInteger('idPermiso');
            $table->unsignedInteger('idRol');
            
            $table->primary(['idPermiso', 'idRol']);
            
            $table->foreign('idPermiso')->references('idPermiso')->on('Permiso');
            $table->foreign('idRol')->references('idRol')->on('Rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RolPermiso');
    }
};
