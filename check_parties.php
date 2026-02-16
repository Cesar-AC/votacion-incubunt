<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Partido;
use App\Models\Cargo;
use App\Models\EstadoElecciones;

echo "=== Base de Datos ===\n";
echo "Partidos: " . Partido::count() . "\n";
echo "Cargos: " . Cargo::count() . "\n";
echo "Estado Elecciones: " . EstadoElecciones::count() . "\n";

if (Partido::count() > 0) {
    echo "\nPartidos disponibles:\n";
    foreach (Partido::all() as $p) {
        echo "  - " . $p->partido . "\n";
    }
}

if (Cargo::count() > 0) {
    echo "\nCargos disponibles:\n";
    foreach (Cargo::all()->take(5) as $c) {
        echo "  - " . $c->cargo . "\n";
    }
}
?>
