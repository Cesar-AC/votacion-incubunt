<?php

namespace Database\Seeders;

use App\Enum\Config;
use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        Configuracion::create([
            'clave' => Config::ELECCION_ACTIVA->value,
            'valor' => '1',
        ]);
    }
}
