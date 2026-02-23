<?php

namespace Database\Seeders;

use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;
use App\Models\Carrera;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\EstadoUsuario;
use App\Models\Partido;
use App\Models\PartidoEleccion;
use App\Models\PadronElectoral;
use App\Models\PerfilUsuario;
use App\Models\TipoVoto;
use App\Models\User;
use App\Models\VotoCandidato;
use App\Models\VotoPartido;
use App\Models\Area;
use App\Models\RolUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class VotacionSeeder extends Seeder
{
    protected \Faker\Generator $faker;
    /**
     * Run the database seeds.
     * Simula un flujo de votación realista con votantes y candidatos de diferentes áreas
     */
    public function run(): void
    {
        $partidos = [
            ['partido' => 'Partido A', 'urlPartido' => 'https://ejemplo.com/partida-a', 'descripcion' => 'Descripción del Partido A'],
            ['partido' => 'Partido B', 'urlPartido' => 'https://ejemplo.com/partida-b', 'descripcion' => 'Descripción del Partido B'],
            ['partido' => 'Partido C', 'urlPartido' => 'https://ejemplo.com/partida-c', 'descripcion' => 'Descripción del Partido C'],
            ['partido' => 'Partido D', 'urlPartido' => 'https://ejemplo.com/partida-d', 'descripcion' => 'Descripción del Partido D'],
            ['partido' => 'Independiente', 'urlPartido' => 'https://ejemplo.com/independiente', 'descripcion' => 'Lista de candidatos independientes'],
        ];

        foreach ($partidos as $partido) {
            Partido::firstOrCreate(
                ['partido' => $partido['partido']],
                $partido
            );
        }

        $faker = Faker::create();
        $this->faker = $faker;

        // ==================== CREAR ELECCIÓN ====================
        $estadoProgramada = EstadoElecciones::where('estado', 'Programada')->first();
        if (!$estadoProgramada) {
            $this->command->error('No existe el estado "Programada". Ejecuta DatosRealesSeeder primero.');
            return;
        }

        $eleccion = Elecciones::create([
            'titulo' => 'Elecciones Generales INCUBUNT 2026',
            'descripcion' => 'Elecciones para cargos de presidencia y directores de área',
            'fechaInicio' => now(),
            'fechaCierre' => now()->addDays(30),
            'idEstado' => $estadoProgramada->idEstado,
        ]);

        $this->command->info("✓ Elección creada: {$eleccion->titulo}");

        // Obtener referencias necesarias
        $estadoActivo = EstadoUsuario::where('nombre', 'Activo')->first();
        $areaPresidencia = Area::where('idArea', Area::PRESIDENCIA)->first();
        $areasDirectores = Area::where('idArea', '!=', Area::PRESIDENCIA)
            ->where('idArea', '!=', Area::SIN_AREA_ASIGNADA)
            ->get();
        $carreras = Carrera::all();
        $partidos = Partido::all();

        if ($areasDirectores->isEmpty() || $carreras->isEmpty() || $partidos->isEmpty()) {
            $this->command->error('Faltan datos base: áreas, carreras o partidos.');
            return;
        }

        $this->command->info('Creando candidatos y asociando partidos con elección...');

        // ==================== ASOCIAR PARTIDOS CON LA ELECCIÓN ====================
        foreach ($partidos as $partido) {
            PartidoEleccion::create([
                'idPartido' => $partido->idPartido,
                'idElecciones' => $eleccion->idElecciones,
            ]);
        }
        $this->command->info("✓ {$partidos->count()} partidos asociados a la elección");

        // ==================== CREAR CANDIDATOS PARA PRESIDENCIA ====================
        $cargosPresidencia = Cargo::where('idArea', Area::PRESIDENCIA)->get();
        $candidatosPresidencia = [];
        $dniCounter = 70000000;

        if ($cargosPresidencia->isEmpty()) {
            $this->command->warn('No hay cargos de presidencia definidos.');
        } else {
            foreach ($partidos as $partido) {
                foreach ($cargosPresidencia as $cargo) {
                    $usuario = User::create([
                        'correo' => "presidencia_{$partido->idPartido}_{$cargo->idCargo}@incubunt.edu.pe",
                        'contraseña' => Hash::make('password'),
                        'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
                    ]);

                    $perfil = PerfilUsuario::create([
                        'idUser' => $usuario->idUser,
                        'nombre' => "Candidato",
                        'apellidoPaterno' => $partido->partido,
                        'apellidoMaterno' => $cargo->cargo,
                        'dni' => (string) $dniCounter++,
                        'telefono' => '9' . rand(10000000, 99999999),
                        'idCarrera' => $carreras->random()->idCarrera,
                        'idArea' => $areaPresidencia->idArea,
                    ]);

                    $candidato = Candidato::create([
                        'idUsuario' => $usuario->idUser,
                        'planTrabajo' => "Plan de trabajo para {$cargo->cargo} del {$partido->partido}",
                    ]);

                    CandidatoEleccion::create([
                        'idCandidato' => $candidato->idCandidato,
                        'idElecciones' => $eleccion->idElecciones,
                        'idPartido' => $partido->idPartido,
                        'idCargo' => $cargo->idCargo,
                    ]);

                    $candidatosPresidencia[] = $candidato;
                }
            }
            $this->command->info("✓ " . count($candidatosPresidencia) . " candidatos de presidencia creados");
        }

        // ==================== CREAR CANDIDATOS PARA DIRECTORES DE ÁREA ====================
        $candidatosDirectores = [];

        foreach ($areasDirectores as $area) {
            $cargoDirector = Cargo::where('idArea', $area->idArea)
                ->where('cargo', 'LIKE', '%Director%')
                ->first();

            if (!$cargoDirector) {
                continue;
            }

            // Crear 2-3 candidatos por área (algunos con partido, otros independientes)
            $numCandidatos = rand(2, 3);
            for ($i = 0; $i < $numCandidatos; $i++) {
                $usuario = User::create([
                    'correo' => "director_{$area->idArea}_{$i}@incubunt.edu.pe",
                    'contraseña' => Hash::make('password'),
                    'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
                ]);

                $perfil = PerfilUsuario::create([
                    'idUser' => $usuario->getKey(),
                    'nombre' => substr($this->faker->firstName, 0, 20),
                    'apellidoPaterno' => substr($this->faker->lastName, 0, 20),
                    'apellidoMaterno' => substr($this->faker->lastName, 0, 20),
                    'dni' => $this->faker->unique()->numerify("########"),
                    'telefono' => '9' . $this->faker->unique()->numerify("########"),
                    'idCarrera' => $carreras->random()->getKey(),
                    'idArea' => $area->getKey(),
                ]);

                $candidato = Candidato::create([
                    'idUsuario' => $usuario->getKey(),
                    'planTrabajo' => "Plan de trabajo para Director de {$area->area}",
                ]);

                // 60% tienen partido, 40% son independientes
                $partidoId = (rand(1, 100) <= 60) ? $partidos->random()->getKey() : null;

                CandidatoEleccion::create([
                    'idCandidato' => $candidato->getKey(),
                    'idElecciones' => $eleccion->getKey(),
                    'idPartido' => $partidoId,
                    'idCargo' => $cargoDirector->getKey(),
                ]);

                $candidatosDirectores[] = $candidato;
            }
        }

        $this->command->info("✓ " . count($candidatosDirectores) . " candidatos directores creados");

        // ==================== CREAR USUARIOS VOTANTES ====================
        $votantes = [];
        $cantidadVotantes = 50;

        for ($i = 1; $i <= $cantidadVotantes; $i++) {
            $area = $areasDirectores->random();
            $carrera = $carreras->random();

            $usuario = User::create([
                'correo' => "votante_{$i}@incubunt.edu.pe",
                'contraseña' => Hash::make('password123'),
                'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
            ]);

            $perfil = PerfilUsuario::create([
                'idUser' => $usuario->getKey(),
                'nombre' => "Votante {$i}",
                'apellidoPaterno' => "Simulado",
                'apellidoMaterno' => "Número {$i}",
                'dni' => (string) $dniCounter++,
                'telefono' => '9' . rand(10000000, 99999999),
                'idArea' => $area->idArea,
                'idCarrera' => $carrera->idCarrera,
            ]);

            RolUser::create([
                'idRol' => 2, // ID Votante
                'idUser' => $usuario->getKey(),
            ]);

            $votantes[] = $usuario;
        }

        $this->command->info("✓ {$cantidadVotantes} votantes creados");

        // ==================== AGREGAR AL PADRÓN ELECTORAL ====================
        foreach ($votantes as $votante) {
            PadronElectoral::firstOrCreate(
                [
                    'idElecciones' => $eleccion->idElecciones,
                    'idUsuario' => $votante->idUser,
                ],
                [
                    'fechaVoto' => null,
                ]
            );
        }

        $this->command->info("✓ Todos los votantes agregados al padrón electoral");

        // ==================== SIMULAR VOTOS ====================
        $votosCandidato = 0;
        $votosPartido = 0;
        $todosLosCandidatos = array_merge($candidatosPresidencia, $candidatosDirectores);

        foreach ($votantes as $votante) {
            $votante->load('perfil.area');

            // 70% vota por candidatos individuales, 30% por partidos
            if (rand(1, 100) <= 70) {
                // Voto a candidato
                $candidato = collect($todosLosCandidatos)->random();
                $candidato->load('usuario.perfil.area');

                if ($candidato->usuario && $candidato->usuario->perfil) {
                    // Determinar tipo de voto según el área
                    $areaVotante = $votante->perfil->area->idArea ?? null;
                    $areaCandidato = $candidato->usuario->perfil->area->idArea ?? null;

                    $tipoVotoId = ($areaVotante && $areaCandidato && $areaVotante === $areaCandidato)
                        ? TipoVoto::ID_MISMA_AREA
                        : TipoVoto::ID_OTRA_AREA;

                    VotoCandidato::create([
                        'idCandidato' => $candidato->idCandidato,
                        'idElecciones' => $eleccion->idElecciones,
                        'idTipoVoto' => $tipoVotoId,
                    ]);

                    // Registrar en padrón que votó
                    PadronElectoral::where('idElecciones', $eleccion->idElecciones)
                        ->where('idUsuario', $votante->idUser)
                        ->update(['fechaVoto' => now()]);

                    $votosCandidato++;
                }
            } else {
                // Voto a partido
                $partido = $partidos->random();

                VotoPartido::create([
                    'idPartido' => $partido->idPartido,
                    'idElecciones' => $eleccion->idElecciones,
                    'idTipoVoto' => TipoVoto::ID_NO_APLICABLE,
                ]);

                // Registrar en padrón que votó
                PadronElectoral::where('idElecciones', $eleccion->idElecciones)
                    ->where('idUsuario', $votante->idUser)
                    ->update(['fechaVoto' => now()]);

                $votosPartido++;
            }
        }

        $this->command->info("✓ {$votosCandidato} votos a candidatos registrados");
        $this->command->info("✓ {$votosPartido} votos a partidos registrados");

        // ==================== RESUMEN ====================
        $totalVotos = $votosCandidato + $votosPartido;
        if ($totalVotos > 0) {
            $percentajeCandidatos = number_format(($votosCandidato / $totalVotos) * 100, 2);
            $percentajePartidos = number_format(($votosPartido / $totalVotos) * 100, 2);
        } else {
            $percentajeCandidatos = 0;
            $percentajePartidos = 0;
        }

        $this->command->info("\n╔═══════════════════════════════════╗");
        $this->command->info("║  RESUMEN SIMULACIÓN DE VOTACIÓN ║");
        $this->command->info("╠═══════════════════════════════════╣");
        $this->command->info("║ Elección: {$eleccion->titulo}");
        $this->command->info("║ Total de votantes: {$cantidadVotantes}");
        $this->command->info("║ Total de votos: {$totalVotos}");
        $this->command->info("║ Votos a candidatos: {$votosCandidato} ({$percentajeCandidatos}%)");
        $this->command->info("║ Votos a partidos: {$votosPartido} ({$percentajePartidos}%)");
        $this->command->info("╚═══════════════════════════════════╝\n");
    }
}
