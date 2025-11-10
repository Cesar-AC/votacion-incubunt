<?php

use App\Models\Permiso;
use App\Models\User;
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
        Schema::create('ExcepcionPermiso', function (Blueprint $table) {
            $table->unsignedInteger('idUser');
            $table->unsignedInteger('idPermiso');
            
            $table->primary(['idUser', 'idPermiso']);
            
            $table->foreign('idUser')->references('idUser')->on('User');
            $table->foreign('idPermiso')->references('idPermiso')->on('Permiso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ExcepcionPermiso');
    }
};
