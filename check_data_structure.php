<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cargo;
use App\Models\Area;

echo "=== ÁREAS Y CARGOS ===\n";
$areas = Area::with('cargos')->get();
foreach ($areas as $area) {
    echo "\nÁrea: {$area->area} (ID: {$area->idArea})\n";
    if ($area->cargos->isEmpty()) {
        echo "  Sin cargos\n";
    } else {
        foreach ($area->cargos as $cargo) {
            echo "  - Cargo: {$cargo->cargo} (ID: {$cargo->idCargo})\n";
        }
    }
}

echo "\n=== CANDIDATOS POR CARGO ===\n";
$cargos = Cargo::with('candidatoElecciones.candidato.usuario.perfil', 'candidatoElecciones.partido')->get();
foreach ($cargos as $cargo) {
    echo "\nCargo: {$cargo->cargo} (ID: {$cargo->idCargo})\n";
    $count = 0;
    foreach ($cargo->candidatoElecciones as $ce) {
        $count++;
        $nombreCandidato = $ce->candidato->usuario->perfil->nombre ?? 'Sin nombre';
        $partition = $ce->partido->partido ?? 'Sin partido';
        echo "  {$count}. {$nombreCandidato} - {$partition}\n";
    }
    if ($count === 0) {
        echo "  Sin candidatos\n";
    }
}
?>
