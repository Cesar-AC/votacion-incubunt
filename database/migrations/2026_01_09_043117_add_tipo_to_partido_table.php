<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('Partido', function (Blueprint $table) {
        $table->enum('tipo', ['LISTA', 'INDIVIDUAL'])
              ->default('LISTA')
              ->after('descripcion');
    });
}

public function down()
{
    Schema::table('Partido', function (Blueprint $table) {
        $table->dropColumn('tipo');
    });
}

};
    