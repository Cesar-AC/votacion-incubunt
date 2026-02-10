<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Candidato;
use App\Models\Elecciones;
use App\Models\CandidatoEleccion;

file_put_contents('check_result.log', "Candidatos: " . Candidato::count() . "\n", FILE_APPEND);
file_put_contents('check_result.log', "Elecciones: " . Elecciones::count() . "\n", FILE_APPEND);
file_put_contents('check_result.log', "CandidatoEleccion: " . CandidatoEleccion::count() . "\n", FILE_APPEND);

if (Candidato::count() > 0) {
    file_put_contents('check_result.log', "\nCandidatos:\n", FILE_APPEND);
    foreach (Candidato::with('usuario.perfil')->get() as $c) {
        file_put_contents('check_result.log', "  - " . ($c->usuario->perfil->nombres ?? 'Sin nombre') . "\n", FILE_APPEND);
    }
}

if (Elecciones::count() > 0) {
    file_put_contents('check_result.log', "\nElecciones:\n", FILE_APPEND);
    foreach (Elecciones::all() as $e) {
        file_put_contents('check_result.log', "  - " . $e->titulo . " (ID: " . $e->idElecciones . ")\n", FILE_APPEND);
    }
}
?>
