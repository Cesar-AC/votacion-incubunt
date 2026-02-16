<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Candidato;
use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\CandidatoEleccion;

echo "Candidatos: " . Candidato::count() . "\n";
echo "Elecciones: " . Elecciones::count() . "\n";
echo "Partidos: " . Partido::count() . "\n";
echo "CandidatoEleccion: " . CandidatoEleccion::count() . "\n";

if (Candidato::count() == 0) {
    echo "\nNo hay candidatos. Ejecutando seeder...\n";
    Artisan::call('db:seed', ['--class' => 'Database\Seeders\CandidatosSeeder']);
    echo "Seeder ejecutado. Candidatos ahora: " . Candidato::count() . "\n";
}
?>
