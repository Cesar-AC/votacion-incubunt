<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\EstadoUsuario;
use App\Models\PerfilUsuario;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidatosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an election
        $estadoProgramada = EstadoElecciones::where('estado', 'Programada')->first() ?? EstadoElecciones::first();
        
        $eleccion = Elecciones::create([
            'titulo' => 'Elecciones Generales 2026',
            'descripcion' => 'Elecciones para directivos de áreas y presidencia',
            'fechaInicio' => now()->addDays(1),
            'fechaCierre' => now()->addDays(7),
            'idEstado' => $estadoProgramada->idEstado ?? 1,
        ]);

        // Get parties
        $partidos = Partido::all();
        if ($partidos->isEmpty()) {
            return; // No parties available
        }

        // Get cargo data
        $cargos = Cargo::all();
        if ($cargos->isEmpty()) {
            return; // No cargos available
        }

        // Get areas (skip Presidencia and Sin Area Asignada)
        $areas = Area::where('idArea', '!=', Area::PRESIDENCIA)
            ->where('idArea', '!=', Area::SIN_AREA_ASIGNADA)
            ->get();

        $estadoActivo = EstadoUsuario::where('nombre', 'Activo')->first() ?? EstadoUsuario::first();

        // Create candidates
        $candidatosData = [
            [
                'nombre' => 'Carlos',
                'otrosNombres' => 'Alberto',
                'apellidoPaterno' => 'García',
                'apellidoMaterno' => 'Rodríguez',
                'dni' => '12345678',
                'planTrabajo' => 'Plan de trabajo enfocado en tecnología e innovación',
            ],
            [
                'nombre' => 'María',
                'otrosNombres' => 'José',
                'apellidoPaterno' => 'López',
                'apellidoMaterno' => 'Martínez',
                'dni' => '23456789',
                'planTrabajo' => 'Plan de trabajo para mejorar la comunicación interna',
            ],
            [
                'nombre' => 'Juan',
                'otrosNombres' => 'Carlos',
                'apellidoPaterno' => 'Pérez',
                'apellidoMaterno' => 'Sánchez',
                'dni' => '34567890',
                'planTrabajo' => 'Plan de trabajo para optimizar procesos administrativos',
            ],
            [
                'nombre' => 'Ana',
                'otrosNombres' => 'María',
                'apellidoPaterno' => 'Fernández',
                'apellidoMaterno' => 'García',
                'dni' => '45678901',
                'planTrabajo' => 'Plan de trabajo para desarrollo de talento humano',
            ],
            [
                'nombre' => 'Pedro',
                'otrosNombres' => 'Luis',
                'apellidoPaterno' => 'Gutiérrez',
                'apellidoMaterno' => 'López',
                'dni' => '56789012',
                'planTrabajo' => 'Plan de trabajo para finanzas y logística',
            ],
            [
                'nombre' => 'Laura',
                'otrosNombres' => 'Patricia',
                'apellidoPaterno' => 'Morales',
                'apellidoMaterno' => 'Ruiz',
                'dni' => '67890123',
                'planTrabajo' => 'Plan de trabajo para marketing y comunicación digital',
            ],
            [
                'nombre' => 'Ricardo',
                'otrosNombres' => 'Javier',
                'apellidoPaterno' => 'Castillo',
                'apellidoMaterno' => 'Hernández',
                'dni' => '78901234',
                'planTrabajo' => 'Plan de trabajo para proyectos especiales y coordinación',
            ],
            [
                'nombre' => 'Sandra',
                'otrosNombres' => 'Lorena',
                'apellidoPaterno' => 'Velázquez',
                'apellidoMaterno' => 'Díaz',
                'dni' => '89012345',
                'planTrabajo' => 'Plan de trabajo para integración de sistemas',
            ],
        ];

        foreach ($candidatosData as $index => $candidatoData) {
            // Create user
            $user = User::create([
                'correo' => strtolower($candidatoData['nombre'] . '.' . $candidatoData['apellidoPaterno'] . '@incubunt.example.com'),
                'contraseña' => bcrypt('password123'),
                'idEstadoUsuario' => $estadoActivo->idEstadoUsuario ?? 1,
            ]);

            // Create user profile
            $area = $areas->random();
            PerfilUsuario::create([
                'idUser' => $user->idUser,
                'nombre' => $candidatoData['nombre'],
                'otrosNombres' => $candidatoData['otrosNombres'],
                'apellidoPaterno' => $candidatoData['apellidoPaterno'],
                'apellidoMaterno' => $candidatoData['apellidoMaterno'],
                'dni' => $candidatoData['dni'],
                'idArea' => $area->idArea,
            ]);

            // Create candidate
            $candidato = Candidato::create([
                'idUsuario' => $user->idUser,
                'planTrabajo' => $candidatoData['planTrabajo'],
            ]);

            // Assign candidate to election with cargo and party
            $cargo = $cargos->random();
            $partido = $partidos->random();

            CandidatoEleccion::create([
                'idCandidato' => $candidato->idCandidato,
                'idElecciones' => $eleccion->idElecciones,
                'idCargo' => $cargo->idCargo,
                'idPartido' => $partido->idPartido,
            ]);
        }
    }
}
