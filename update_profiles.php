<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Candidato;
use App\Models\PerfilUsuario;

echo "=== ACTUALIZANDO PERFILES ===\n";

// Get all candidates and update their profiles
$candidatos = Candidato::with('usuario.perfil')->get();
$nombres = ['Juan', 'María', 'Carlos', 'Ana', 'Pedro', 'Laura', 'Ricardo', 'Sandra'];
$apellidos = ['García', 'López', 'Martínez', 'Rodríguez', 'Fernández', 'Gutiérrez', 'Morales', 'Castillo'];

$index = 0;
foreach ($candidatos as $candidato) {
    if ($candidato->usuario && $candidato->usuario->perfil) {
        $perfil = $candidato->usuario->perfil;
        
        // Check if name is empty or "Sin nombre"
        if (empty($perfil->nombre) || $perfil->nombre === 'Sin nombre') {
            $nombre = $nombres[$index % count($nombres)];
            $apellido = $apellidos[$index % count($apellidos)];
            
            $perfil->update([
                'nombre' => $nombre,
                'apellidoPaterno' => $apellido,
                'apellidoMaterno' => 'Test'
            ]);
            
            echo "✓ Actualizado: {$nombre} {$apellido} (Candidato ID: {$candidato->idCandidato})\n";
        }
    }
    $index++;
}

echo "\n✓ Proceso completado\n";
?>
