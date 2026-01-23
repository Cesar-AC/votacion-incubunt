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
        Schema::table('Candidato', function (Blueprint $table) {
            $table->text('planTrabajo')->nullable()->after('idUsuario');
        });

        Schema::table('Partido', function (Blueprint $table) {
            $table->text('planTrabajo')->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Candidato', function (Blueprint $table) {
            $table->dropColumn('planTrabajo');
        });

        Schema::table('Partido', function (Blueprint $table) {
            $table->dropColumn('planTrabajo');
        });
    }
};
