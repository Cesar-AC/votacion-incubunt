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
        Schema::create('PropuestaPartido', function (Blueprint $table) {
            $table->increments('idPropuesta');
            $table->string('propuesta', 255);
            $table->text('descripcion');
            $table->unsignedInteger('idPartido');
            
            $table->foreign('idPartido')->references('idPartido')->on('Partido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PropuestaPartido');
    }
};
