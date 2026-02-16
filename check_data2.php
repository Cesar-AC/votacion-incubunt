<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cargo;
use App\Models\Area;
use App\Models\CandidatoEleccion;

$output = "=== ÁREAS Y CARGOS ===\n";
$areas = Area::with('cargos')->get();
foreach ($areas as $area) {
    $output .= "\nÁrea: {$area->area} (ID: {$area->idArea})\n";
    if ($area->cargos->isEmpty()) {
        $output .= "  Sin cargos\n";
    } else {
        foreach ($area->cargos as $cargo) {
            $output .= "  - Cargo: {$cargo->cargo} (ID: {$cargo->idCargo})\n";
        }
    }
}

$output .= "\n\n=== CANDIDATOS POR CARGO ===\n";
$cargos = Cargo::all();
foreach ($cargos as $cargo) {
    $output .= "\nCargo: {$cargo->cargo} (ID: {$cargo->idCargo})\n";
    $ces = CandidatoEleccion::where('idCargo', $cargo->idCargo)
        ->with('candidato.usuario.perfil', 'partido')
        ->get();
    
    if ($ces->isEmpty()) {
        $output .= "  Sin candidatos\n";
    } else {
        $count = 0;
        foreach ($ces as $ce) {
            $count++;
            $nombreCandidato = $ce->candidato->usuario->perfil->nombre ?? 'Sin nombre';
            $partition = $ce->partido->partido ?? 'Sin partido';
            $output .= "  {$count}. {$nombreCandidato} - {$partition}\n";
        }
    }
}

file_put_contents('data_check.log', $output);
echo $output;
?>
