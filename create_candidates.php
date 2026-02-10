<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\PerfilUsuario;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\PartidoEleccion;
use App\Models\Elecciones;
use App\Models\Cargo;
use App\Models\Partido;
use App\Models\EstadoUsuario;
use App\Models\Area;

// Get active election
$eleccion = Elecciones::where('titulo', 'Elecciones Generales 2026')->first();
if (!$eleccion) {
    echo "No hay elección. Necesitas crear una primero.\n";
    exit;
}

// Get election state
$estadoActivo = EstadoUsuario::where('nombre', 'Activo')->first();

// Get parties and cargos
$partidos = Partido::all();
$cargos = Cargo::where('cargo', 'Director de Área')->get();

echo "Elección: {$eleccion->titulo}\n";
echo "Partidos disponibles: " . $partidos->count() . "\n";
echo "Cargos disponibles: " . $cargos->count() . "\n";

if ($partidos->isEmpty()) {
    echo "No hay partidos disponibles.\n";
    exit;
}

if ($cargos->isEmpty()) {
    echo "No hay cargos disponibles.\n";
    exit;
}

// Create candidates
$nombres = [
    ['nombre' => 'Juan', 'apellido' => 'Pérez', 'dni' => '12345678'],
    ['nombre' => 'María', 'apellido' => 'García', 'dni' => '23456789'],
    ['nombre' => 'Carlos', 'apellido' => 'López', 'dni' => '34567890'],
    ['nombre' => 'Ana', 'apellido' => 'Martínez', 'dni' => '45678901'],
    ['nombre' => 'Pedro', 'apellido' => 'Rodríguez', 'dni' => '56789012'],
    ['nombre' => 'Laura', 'apellido' => 'Fernández', 'dni' => '67890123'],
    ['nombre' => 'Ricardo', 'apellido' => 'Gutiérrez', 'dni' => '78901234'],
    ['nombre' => 'Sandra', 'apellido' => 'Morales', 'dni' => '89012345'],
];

$createdCount = 0;
foreach ($nombres as $index => $personaData) {
    try {
        // Create or update user
        $user = User::updateOrCreate(
            ['correo' => strtolower($personaData['nombre'] . '.' . $personaData['apellido'] . '@test.com')],
            [
                'contraseña' => bcrypt('password123'),
                'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
            ]
        );

        // Create or update profile
        PerfilUsuario::updateOrCreate(
            ['idUser' => $user->idUser],
            [
                'nombre' => $personaData['nombre'],
                'apellidoPaterno' => $personaData['apellido'],
                'apellidoMaterno' => 'Test',
                'dni' => $personaData['dni'],
                'idArea' => Area::whereNotIn('idArea', [Area::PRESIDENCIA, Area::SIN_AREA_ASIGNADA])->first()->idArea ?? 1,
            ]
        );

        // Create or update candidate
        $candidato = Candidato::firstOrCreate(
            ['idUsuario' => $user->idUser],
            ['planTrabajo' => 'Plan de trabajo para ' . $personaData['nombre']]
        );

        // Assign candidate to cargo with party
        $cargo = $cargos->random();
        $partido = $partidos->random();

        CandidatoEleccion::firstOrCreate(
            [
                'idCandidato' => $candidato->idCandidato,
                'idElecciones' => $eleccion->idElecciones,
                'idCargo' => $cargo->idCargo,
            ],
            ['idPartido' => $partido->idPartido]
        );

        $createdCount++;
        echo "✓ Candidato creado: {$personaData['nombre']} {$personaData['apellido']} (Partido: {$partido->partido}, Cargo: {$cargo->cargo})\n";
    } catch (\Exception $e) {
        echo "✗ Error creando candidato {$personaData['nombre']}: " . $e->getMessage() . "\n";
    }
}

// Ensure parties are linked to election
foreach ($partidos as $partido) {
    PartidoEleccion::firstOrCreate(
        [
            'idPartido' => $partido->idPartido,
            'idElecciones' => $eleccion->idElecciones,
        ]
    );
}

echo "\n✓ Proceso completado. Candidatos creados: $createdCount\n";
?>
