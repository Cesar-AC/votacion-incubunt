<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Partido, App\Models\Cargo;

file_put_contents('debug.log', "Partidos: " . Partido::count() . "\n", FILE_APPEND);
file_put_contents('debug.log', "Cargos: " . Cargo::count() . "\n", FILE_APPEND);
?>
