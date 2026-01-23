<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\Candidato;
use App\Models\PropuestaPartido;
use App\Models\PropuestaCandidato;
use App\Models\User;
use App\Models\PerfilUsuario;
use App\Models\Carrera;
use App\Models\Cargo;
use App\Models\Area;
use App\Models\EstadoUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PropuestasSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener o crear una elección activa
        $eleccion = Elecciones::first();
        if (!$eleccion) {
            $eleccion = Elecciones::create([
                'titulo' => 'Elecciones Incubunt 2026',
                'descripcion' => 'Elección de nueva directiva para el periodo 2026',
                'fechaInicio' => now(),
                'fechaCierre' => now()->addMonths(1),
                'idEstado' => 1,
            ]);
        }

        // Obtener o crear carreras
        $carreras = Carrera::all();
        if ($carreras->isEmpty()) {
            $carreras = collect([
                Carrera::create(['carrera' => 'Ingeniería de Sistemas']),
                Carrera::create(['carrera' => 'Administración']),
                Carrera::create(['carrera' => 'Derecho']),
                Carrera::create(['carrera' => 'Economía']),
                Carrera::create(['carrera' => 'Contabilidad']),
                Carrera::create(['carrera' => 'Marketing']),
                Carrera::create(['carrera' => 'Ingeniería Industrial']),
            ]);
        }

        // Obtener estado activo de usuario
        $estadoActivo = EstadoUsuario::firstOrCreate(
            ['nombre' => 'Activo'],
            ['nombre' => 'Activo']
        );

        // ==================== CREAR PARTIDOS ====================
        $partidos = [
            [
                'nombre' => 'Sinergia Estudiantil',
                'descripcion' => 'Somos un equipo multidisciplinario comprometido con potenciar el ecosistema emprendedor de Incubunt. Creemos en la fuerza de la unión entre facultades para crear líderes integrales y proyectos innovadores que impacten en la sociedad.',
                'propuestas' => [
                    'Crear la "Incubadora Junior" para alumnos de primeros ciclos con mentoría personalizada',
                    'Establecer alianzas estratégicas con empresas del Parque Industrial de Trujillo',
                    'Digitalización total de los procesos de membresía y gestión administrativa',
                    'Implementar programa de becas para miembros destacados',
                ]
            ],
            [
                'nombre' => 'Impulso Universitario',
                'descripcion' => 'Buscamos transformar Incubunt en un referente nacional de emprendimiento universitario. Nuestra gestión se centrará en la visibilidad externa, fortalecer las habilidades blandas de cada asociado y crear redes de networking estratégicas.',
                'propuestas' => [
                    'Organizar Feria de Talento Incubunt semestral abierta al público con inversionistas',
                    'Desarrollar Programa de Mentoría "Senior a Junior" dentro de la organización',
                    'Ofrecer capacitaciones certificadas en metodologías ágiles y design thinking',
                    'Crear convenios con universidades internacionales para intercambios',
                ]
            ],
            [
                'nombre' => 'Nexo Emprendedor',
                'descripcion' => 'La conexión es clave en el emprendimiento. Proponemos una gestión transparente y horizontal, donde cada área tenga voz y voto en las decisiones estratégicas desde el primer día, fomentando la colaboración interdisciplinaria.',
                'propuestas' => [
                    'Desarrollar plataforma web para bolsa de trabajo exclusiva de miembros',
                    'Organizar Hackathon inter-universitaria con premios y reconocimientos',
                    'Reestructurar los comités de ética y transparencia organizacional',
                    'Implementar sistema de votación digital para todas las decisiones importantes',
                ]
            ],
        ];

        $partidosCreados = [];
        foreach ($partidos as $partidoData) {
            $partido = Partido::create([
                'partido' => $partidoData['nombre'],
                'urlPartido' => 'https://incubunt.edu.pe/' . strtolower(str_replace(' ', '-', $partidoData['nombre'])),
                'descripcion' => $partidoData['descripcion'],
                'tipo' => 'LISTA',
                'planTrabajo' => 'https://google.com/',
            ]);

            // Asociar partido con elección
            DB::table('PartidoEleccion')->insert([
                'idPartido' => $partido->idPartido,
                'idElecciones' => $eleccion->idElecciones,
            ]);

            // Crear propuestas del partido
            foreach ($partidoData['propuestas'] as $propuesta) {
                PropuestaPartido::create([
                    'propuesta' => substr($propuesta, 0, 100),
                    'descripcion' => $propuesta,
                    'idPartido' => $partido->idPartido,
                ]);
            }

            $partidosCreados[] = $partido;
        }

        // ==================== OBTENER O CREAR CARGOS Y ÁREAS ====================

        // Áreas primero
        $area = Area::firstOrCreate(
            ['area' => 'Administración'],
            ['area' => 'Administración']
        );

        // Cargos presidenciales
        $cargoPresidente = Cargo::firstOrCreate(
            ['cargo' => 'Presidente'],
            ['cargo' => 'Presidente', 'idArea' => $area->idArea]
        );

        $cargoVicepresidente = Cargo::firstOrCreate(
            ['cargo' => 'Vicepresidente'],
            ['cargo' => 'Vicepresidente', 'idArea' => $area->idArea]
        );

        // Áreas y sus cargos
        $areasData = [
            'Tecnología (TI)' => 'Director de TI',
            'Marketing' => 'Director de Marketing',
            'Recursos Humanos' => 'Director de RRHH',
            'Logística' => 'Director de Logística',
            'PMO (Proyectos)' => 'Director de PMO',
        ];

        $cargosArea = [];
        foreach ($areasData as $areaNombre => $cargoNombre) {
            $area = Area::firstOrCreate(
                ['area' => $areaNombre],
                ['area' => $areaNombre]
            );

            $cargo = Cargo::firstOrCreate(
                ['cargo' => $cargoNombre],
                ['cargo' => $cargoNombre, 'idArea' => $area->idArea]
            );

            $cargosArea[$areaNombre] = $cargo;
        }

        // ==================== CREAR CANDIDATOS POR PARTIDO ====================

        $candidatosPartido = [
            [
                'nombre' => 'Carlos',
                'apellidoPaterno' => 'Mendez',
                'apellidoMaterno' => 'Torres',
                'dni' => '72345678',
                'carrera' => 'Ingeniería Industrial',
                'cargo' => 'Presidente',
                'propuestas' => [
                    'Implementar sistema de seguimiento de proyectos con KPIs claros y medibles',
                    'Crear fondo semilla para proyectos de miembros destacados con capital inicial',
                ]
            ],
            [
                'nombre' => 'Ana María',
                'apellidoPaterno' => 'Torres',
                'apellidoMaterno' => 'Ríos',
                'dni' => '72345679',
                'carrera' => 'Administración',
                'cargo' => 'Vicepresidente',
                'propuestas' => [
                    'Desarrollar manual de procedimientos para todas las áreas de la organización',
                    'Implementar sistema de evaluación de desempeño trimestral objetivo',
                ]
            ],
            [
                'nombre' => 'Maria Fe',
                'apellidoPaterno' => 'Castro',
                'apellidoMaterno' => 'Ruiz',
                'dni' => '72345680',
                'carrera' => 'Economía',
                'cargo' => 'Presidente',
                'propuestas' => [
                    'Crear programa de educación financiera para emprendedores del ecosistema',
                    'Implementar modelo de sostenibilidad financiera para la organización',
                ]
            ],
            [
                'nombre' => 'Jorge Luis',
                'apellidoPaterno' => 'Ruiz',
                'apellidoMaterno' => 'Pérez',
                'dni' => '72345681',
                'carrera' => 'Ingeniería de Sistemas',
                'cargo' => 'Vicepresidente',
                'propuestas' => [
                    'Desarrollar app móvil oficial de Incubunt con funcionalidades completas',
                    'Automatizar procesos administrativos con IA y machine learning',
                ]
            ],
            [
                'nombre' => 'Roberto',
                'apellidoPaterno' => 'Diaz',
                'apellidoMaterno' => 'Sánchez',
                'dni' => '72345682',
                'carrera' => 'Derecho',
                'cargo' => 'Presidente',
                'propuestas' => [
                    'Crear clínica jurídica para asesoría legal gratuita a emprendedores',
                    'Desarrollar programa de compliance para startups con normativas vigentes',
                ]
            ],
            [
                'nombre' => 'Sofia',
                'apellidoPaterno' => 'Chang',
                'apellidoMaterno' => 'Li',
                'dni' => '72345683',
                'carrera' => 'Marketing',
                'cargo' => 'Vicepresidente',
                'propuestas' => [
                    'Implementar estrategia de content marketing para posicionar Incubunt',
                    'Crear curso de personal branding para miembros y networking efectivo',
                ]
            ],
        ];

        // Crear candidatos presidenciales por partido
        $dniCounter = 72345678;
        foreach ($partidosCreados as $index => $partido) {
            // Presidente
            $presidente = $candidatosPartido[$index * 2];
            $userPresidente = User::create([
                'correo' => strtolower(str_replace(' ', '.', $presidente['nombre'])) . '.' . strtolower($presidente['apellidoPaterno']) . '@unitru.edu.pe',
                'contraseña' => Hash::make('password'),
                'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
            ]);

            $perfilPresidente = PerfilUsuario::create([
                'idUser' => $userPresidente->idUser,
                'nombre' => $presidente['nombre'],
                'apellidoPaterno' => $presidente['apellidoPaterno'],
                'apellidoMaterno' => $presidente['apellidoMaterno'],
                'dni' => $presidente['dni'],
                'telefono' => '9' . rand(10000000, 99999999),
                'idCarrera' => $carreras->where('carrera', 'LIKE', '%' . explode(' ', $presidente['carrera'])[0] . '%')->first()->idCarrera ?? 1,
                'idArea' => $area->idArea,
            ]);

            $candidatoPresidente = Candidato::create([
                'idPartido' => $partido->idPartido,
                'idCargo' => $cargoPresidente->idCargo,
                'idUsuario' => $userPresidente->idUser,
                'planTrabajo' => 'https://google.com/',
            ]);

            // Asociar candidato con elección
            DB::table('CandidatoEleccion')->insert([
                'idCandidato' => $candidatoPresidente->idCandidato,
                'idElecciones' => $eleccion->idElecciones,
            ]);

            foreach ($presidente['propuestas'] as $propuesta) {
                PropuestaCandidato::create([
                    'propuesta' => substr($propuesta, 0, 100),
                    'descripcion' => $propuesta,
                    'idCandidato' => $candidatoPresidente->idCandidato,
                ]);
            }

            // Vicepresidente
            $vicepresidente = $candidatosPartido[$index * 2 + 1];
            $userVice = User::create([
                'correo' => strtolower(str_replace(' ', '.', $vicepresidente['nombre'])) . '.' . strtolower($vicepresidente['apellidoPaterno']) . '@unitru.edu.pe',
                'contraseña' => Hash::make('password'),
                'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
            ]);

            $perfilVice = PerfilUsuario::create([
                'idUser' => $userVice->idUser,
                'nombre' => $vicepresidente['nombre'],
                'apellidoPaterno' => $vicepresidente['apellidoPaterno'],
                'apellidoMaterno' => $vicepresidente['apellidoMaterno'],
                'dni' => $vicepresidente['dni'],
                'telefono' => '9' . rand(10000000, 99999999),
                'idCarrera' => $carreras->where('carrera', 'LIKE', '%' . explode(' ', $vicepresidente['carrera'])[0] . '%')->first()->idCarrera ?? 2,
                'idArea' => $area->idArea,
            ]);

            $candidatoVice = Candidato::create([
                'idPartido' => $partido->idPartido,
                'idCargo' => $cargoVicepresidente->idCargo,
                'idUsuario' => $userVice->idUser,
                'planTrabajo' => 'https://google.com/',
            ]);

            DB::table('CandidatoEleccion')->insert([
                'idCandidato' => $candidatoVice->idCandidato,
                'idElecciones' => $eleccion->idElecciones,
            ]);

            foreach ($vicepresidente['propuestas'] as $propuesta) {
                PropuestaCandidato::create([
                    'propuesta' => substr($propuesta, 0, 100),
                    'descripcion' => $propuesta,
                    'idCandidato' => $candidatoVice->idCandidato,
                ]);
            }
        }

        // ==================== CREAR CANDIDATOS POR ÁREA ====================

        $candidatosAreas = [
            'Tecnología (TI)' => [
                [
                    'nombre' => 'David',
                    'apellidoPaterno' => 'Code',
                    'apellidoMaterno' => 'Vargas',
                    'dni' => '72345684',
                    'carrera' => 'Ingeniería de Sistemas',
                    'propuestas' => [
                        'Implementar intranet para gestión de asistencia y proyectos internos',
                        'Organizar taller de Python para no programadores en la organización',
                        'Mejorar infraestructura wifi y equipamiento en oficinas',
                    ]
                ],
                [
                    'nombre' => 'Sara',
                    'apellidoPaterno' => 'Data',
                    'apellidoMaterno' => 'Rojas',
                    'dni' => '72345685',
                    'carrera' => 'Ingeniería de Sistemas',
                    'propuestas' => [
                        'Crear dashboard de métricas en tiempo real para seguimiento',
                        'Migración a la nube de archivos históricos para mejor acceso',
                        'Implementar sistema de análisis predictivo de participación',
                    ]
                ],
            ],
            'Marketing' => [
                [
                    'nombre' => 'Lucia',
                    'apellidoPaterno' => 'Brand',
                    'apellidoMaterno' => 'Silva',
                    'dni' => '72345686',
                    'carrera' => 'Marketing',
                    'propuestas' => [
                        'Rediseño total de identidad visual en redes sociales',
                        'Crear equipo de cobertura audiovisual profesional interno',
                        'Desarrollar merchandising oficial de Incubunt para eventos',
                    ]
                ],
                [
                    'nombre' => 'Mario',
                    'apellidoPaterno' => 'Ads',
                    'apellidoMaterno' => 'Cortez',
                    'dni' => '72345687',
                    'carrera' => 'Marketing',
                    'propuestas' => [
                        'Estrategia de TikTok para viralización orgánica de contenido',
                        'Newsletter mensual para mantener conectados a egresados',
                        'Alianzas con influencers universitarios de la región norte',
                    ]
                ],
            ],
            'Recursos Humanos' => [
                [
                    'nombre' => 'Camila',
                    'apellidoPaterno' => 'People',
                    'apellidoMaterno' => 'Vega',
                    'dni' => '72345688',
                    'carrera' => 'Administración',
                    'propuestas' => [
                        'Programa de Buddies para nuevos ingresos con mentores',
                        'Evaluaciones de clima organizacional trimestrales objetivas',
                        'Línea de escucha activa y apoyo emocional para miembros',
                    ]
                ],
            ],
            'Logística' => [
                [
                    'nombre' => 'Renzo',
                    'apellidoPaterno' => 'Stock',
                    'apellidoMaterno' => 'Paredes',
                    'dni' => '72345689',
                    'carrera' => 'Ingeniería Industrial',
                    'propuestas' => [
                        'Digitalizar inventario con códigos QR para mejor control',
                        'Establecer proveedores fijos con descuentos corporativos',
                        'Optimizar gestión de coffee breaks en eventos masivos',
                    ]
                ],
            ],
            'PMO (Proyectos)' => [
                [
                    'nombre' => 'Victor',
                    'apellidoPaterno' => 'Scrum',
                    'apellidoMaterno' => 'Zamora',
                    'dni' => '72345690',
                    'carrera' => 'Ingeniería Industrial',
                    'propuestas' => [
                        'Estandarizar tableros Trello/Notion para todas las áreas',
                        'Ofrecer capacitación en PMI para miembros interesados',
                        'Crear banco de proyectos sociales con impacto medible',
                    ]
                ],
            ],
        ];

        foreach ($candidatosAreas as $areaNombre => $candidatos) {
            $cargo = $cargosArea[$areaNombre];
            $areaObj = Area::where('area', $areaNombre)->first();

            foreach ($candidatos as $candidatoData) {
                $user = User::create([
                    'correo' => strtolower($candidatoData['nombre']) . '.' . strtolower($candidatoData['apellidoPaterno']) . '@unitru.edu.pe',
                    'contraseña' => Hash::make('password'),
                    'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
                ]);

                $perfil = PerfilUsuario::create([
                    'idUser' => $user->idUser,
                    'nombre' => $candidatoData['nombre'],
                    'apellidoPaterno' => $candidatoData['apellidoPaterno'],
                    'apellidoMaterno' => $candidatoData['apellidoMaterno'],
                    'dni' => $candidatoData['dni'],
                    'telefono' => '9' . rand(10000000, 99999999),
                    'idCarrera' => $carreras->where('carrera', 'LIKE', '%' . explode(' ', $candidatoData['carrera'])[0] . '%')->first()->idCarrera ?? 1,
                    'idArea' => $areaObj->idArea,
                ]);

                // Asignar un partido aleatorio
                $partidoAleatorio = $partidosCreados[array_rand($partidosCreados)];

                $candidato = Candidato::create([
                    'idPartido' => $partidoAleatorio->idPartido,
                    'idCargo' => $cargo->idCargo,
                    'idUsuario' => $user->idUser,
                    'planTrabajo' => 'https://google.com/',
                ]);

                DB::table('CandidatoEleccion')->insert([
                    'idCandidato' => $candidato->idCandidato,
                    'idElecciones' => $eleccion->idElecciones,
                ]);

                foreach ($candidatoData['propuestas'] as $propuesta) {
                    PropuestaCandidato::create([
                        'propuesta' => substr($propuesta, 0, 100),
                        'descripcion' => $propuesta,
                        'idCandidato' => $candidato->idCandidato,
                    ]);
                }
            }
        }

        $this->command->info('✅ Seeder de propuestas completado exitosamente!');
        $this->command->info('📊 Partidos creados: ' . count($partidosCreados));
        $this->command->info('👥 Total de candidatos creados: ' . Candidato::count());
        $this->command->info('📝 Total de propuestas creadas: ' . (PropuestaCandidato::count() + PropuestaPartido::count()));
    }
}
