<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\EstadoUsuario;
use App\Models\PerfilUsuario;
use App\Models\Partido;
use App\Models\PartidoEleccion;
use App\Models\User;

file_put_contents('populate_result.log', "=== INICIANDO POBLACIÓN DE BASE DE DATOS ===\n", FILE_APPEND);

// Verificar data existente
file_put_contents('populate_result.log', "\nCandidatos: " . Candidato::count() . "\n", FILE_APPEND);
file_put_contents('populate_result.log', "Cargos: " . Cargo::count() . "\n", FILE_APPEND);
file_put_contents('populate_result.log', "Partidos: " . Partido::count() . "\n", FILE_APPEND);
file_put_contents('populate_result.log', "Elecciones: " . Elecciones::count() . "\n", FILE_APPEND);

if (Candidato::count() > 0 && Candidato::whereHas('usuario.perfil')->count() > 0) {
    file_put_contents('populate_result.log', "\nLa base de datos ya tiene candidatos con perfiles.\n", FILE_APPEND);
    exit;
}

// Limpiar datos anteriores
file_put_contents('populate_result.log', "\nLimpiando datos anteriores...\n", FILE_APPEND);
CandidatoEleccion::truncate();
PartidoEleccion::truncate();
Candidato::truncate();
PerfilUsuario::truncate();
User::where('correo', '!=', 'admin@example.com')->delete();

// Obtener o crear elección
$estadoProgramada = EstadoElecciones::where('estado', 'Programada')->first();
$eleccion = Elecciones::firstOrCreate(
    ['titulo' => 'Elecciones Generales 2026'],
    [
        'descripcion' => 'Elecciones para directivos de áreas y presidencia',
        'fechaInicio' => now()->addDays(1),
        'fechaCierre' => now()->addDays(7),
        'idEstado' => $estadoProgramada->idEstado ?? 1,
    ]
);
file_put_contents('populate_result.log', "Elección: " . $eleccion->titulo . " (ID: " . $eleccion->idElecciones . ")\n", FILE_APPEND);

// Obtener partidos
$partidos = Partido::all();
file_put_contents('populate_result.log', "Partidos disponibles: " . $partidos->count() . "\n", FILE_APPEND);

if ($partidos->isEmpty()) {
    file_put_contents('populate_result.log', "ERROR: No hay partidos en la base de datos\n", FILE_APPEND);
    exit;
}

// Asociar partidos a elección
foreach ($partidos as $p) {
    PartidoEleccion::firstOrCreate([
        'idPartido' => $p->idPartido,
        'idElecciones' => $eleccion->idElecciones,
    ]);
}
file_put_contents('populate_result.log', "Partidos asociados a elección\n", FILE_APPEND);

// Obtener cargo Director de Área
$cargoDirector = Cargo::where('cargo', 'Director de Área')->first();
if (!$cargoDirector) {
    file_put_contents('populate_result.log', "ERROR: No existe cargo 'Director de Área'\n", FILE_APPEND);
    exit;
}

// Crear candidatos
$estadoActivo = EstadoUsuario::where('nombre', 'Activo')->first();
$candidatosData = [
    ['nombre' => 'Carlos', 'apellidoPaterno' => 'García', 'apellidoMaterno' => 'Rodríguez', 'dni' => '12345678A'],
    ['nombre' => 'María', 'apellidoPaterno' => 'López', 'apellidoMaterno' => 'Martínez', 'dni' => '23456789B'],
    ['nombre' => 'Juan', 'apellidoPaterno' => 'Pérez', 'apellidoMaterno' => 'Sánchez', 'dni' => '34567890C'],
    ['nombre' => 'Ana', 'apellidoPaterno' => 'Fernández', 'apellidoMaterno' => 'García', 'dni' => '45678901D'],
    ['nombre' => 'Pedro', 'apellidoPaterno' => 'Gutiérrez', 'apellidoMaterno' => 'López', 'dni' => '56789012E'],
    ['nombre' => 'Laura', 'apellidoPaterno' => 'Morales', 'apellidoMaterno' => 'Ruiz', 'dni' => '67890123F'],
];

$count = 0;
foreach ($candidatosData as $idx => $data) {
    try {
        $email = strtolower($data['nombre'] . '.' . $data['apellidoPaterno'] . '@incubunt.local');
        
        $user = User::firstOrCreate(
            ['correo' => $email],
            [
                'contraseña' => bcrypt('password123'),
                'idEstadoUsuario' => $estadoActivo->idEstadoUsuario ?? 1,
            ]
        );

        // Crear o actualizar perfil
        PerfilUsuario::updateOrCreate(
            ['idUser' => $user->idUser],
            [
                'nombre' => $data['nombre'],
                'apellidoPaterno' => $data['apellidoPaterno'],
                'apellidoMaterno' => $data['apellidoMaterno'],
                'dni' => $data['dni'],
            ]
        );

        // Crear candidato
        $candidato = Candidato::firstOrCreate(
            ['idUsuario' => $user->idUser],
            ['planTrabajo' => 'Plan de trabajo de ' . $data['nombre']]
        );

        // Asociar a elección con partido y cargo
        $partido = $partidos->random();
        CandidatoEleccion::firstOrCreate([
            'idCandidato' => $candidato->idCandidato,
            'idElecciones' => $eleccion->idElecciones,
            'idCargo' => $cargoDirector->idCargo,
            'idPartido' => $partido->idPartido,
        ]);

        $count++;
        file_put_contents('populate_result.log', "✓ Candidato creado: " . $data['nombre'] . " " . $data['apellidoPaterno'] . "\n", FILE_APPEND);
    } catch (\Exception $e) {
        file_put_contents('populate_result.log', "✗ Error con " . $data['nombre'] . ": " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

file_put_contents('populate_result.log', "\nCandidatos creados: " . $count . "\n", FILE_APPEND);
file_put_contents('populate_result.log', "Candidatos totales: " . Candidato::count() . "\n", FILE_APPEND);
file_put_contents('populate_result.log', "CandidatoEleccion totales: " . CandidatoEleccion::count() . "\n", FILE_APPEND);
file_put_contents('populate_result.log', "\n=== POBLACIÓN COMPLETADA ===\n", FILE_APPEND);
?>
