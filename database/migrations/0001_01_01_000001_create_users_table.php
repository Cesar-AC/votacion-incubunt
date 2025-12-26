ALTER TABLE `User` ADD COLUMN `correo` VARCHAR(100) NOT NULL UNIQUE;
ALTER TABLE `User` ADD COLUMN `contraseña` TEXT NOT NULL;
ALTER TABLE `User` ADD COLUMN `idEstadoUsuario` INT UNSIGNED NOT NULL;<?php

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
        Schema::create('User', function (Blueprint $table) {
            $table->increments('idUser');
            $table->string('correo', 100)->unique();
            $table->text('contraseña');
            $table->unsignedInteger('idEstadoUsuario');
            $table->foreign('idEstadoUsuario')->references('idEstadoUsuario')->on('EstadoUsuario');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('correo')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('User');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
