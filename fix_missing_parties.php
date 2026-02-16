<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Partido;

echo "=== ACTUALIZANDO CANDIDATOS SIN PARTIDO ===\n";

// Get CandidatoElecciones with null partido
$cesWithoutParty = DB::table('CandidatoEleccion')
    ->whereNull('idPartido')
    ->get();

if ($cesWithoutParty->isEmpty()) {
    echo "Todos los candidatos tienen partido asignado.\n";
} else {
    echo "Encontrados " . $cesWithoutParty->count() . " candidatos sin partido.\n";
    $parties = Partido::pluck('idPartido')->toArray();
    
    foreach ($cesWithoutParty as $ce) {
        $randomPartyId = $parties[array_rand($parties)];
        DB::table('CandidatoEleccion')
            ->where('idCandidato', $ce->idCandidato)
            ->where('idElecciones', $ce->idElecciones)
            ->update(['idPartido' => $randomPartyId]);
        
        $party = Partido::find($randomPartyId);
        echo "  ✓ Asignado: Candidato {$ce->idCandidato} -> {$party->partido}\n";
    }
}

echo "\n✓ Proceso completado\n";
?>
