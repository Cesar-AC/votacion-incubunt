<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $seeder = new \Database\Seeders\CandidatosSeeder();
    $seeder->run();
    file_put_contents('seed_result.log', "Seeder ejecutado exitosamente\n");
} catch (\Exception $e) {
    file_put_contents('seed_result.log', "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}
?>
