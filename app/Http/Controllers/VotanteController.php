<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotanteController extends Controller
{
    /**
     * Página principal del votante - CON DATOS DE PRUEBA
     */
    public function home()
    {
        // Datos estáticos de prueba
        $eleccionActiva = (object)[
            'id' => 1,
            'nombreEleccion' => 'Elecciones Estudiantiles 2026',
            'descripcion' => 'Elecciones para renovar el Consejo Estudiantil',
            'fechaInicio' => now()->subDays(2),
            'fechaFin' => now()->addDays(5),
            'estadoEleccionesId' => 1
        ];
        
        $totalElecciones = 3;
        
        return view('votante.home', compact('eleccionActiva', 'totalElecciones'));
    }

    /**
     * Lista todas las elecciones - CON DATOS DE PRUEBA
     */
    public function listarElecciones()
    {
        // Crear datos de prueba
        $elecciones = collect([
            (object)[
                'id' => 1,
                'nombreEleccion' => 'Elecciones Estudiantiles 2026',
                'descripcion' => 'Elecciones para renovar el Consejo Estudiantil',
                'fechaInicio' => now()->subDays(2),
                'fechaFin' => now()->addDays(5),
                'estadoEleccionesId' => 1,
                'estadoEleccion' => (object)['nombre' => 'Activo'],
                'candidatos' => collect([
                    (object)['id' => 1], (object)['id' => 2], (object)['id' => 3]
                ])
            ],
            (object)[
                'id' => 2,
                'nombreEleccion' => 'Elecciones de Facultad 2026',
                'descripcion' => 'Elecciones para delegados de facultad',
                'fechaInicio' => now()->addDays(10),
                'fechaFin' => now()->addDays(17),
                'estadoEleccionesId' => 2,
                'estadoEleccion' => (object)['nombre' => 'Programado'],
                'candidatos' => collect([
                    (object)['id' => 4], (object)['id' => 5]
                ])
            ]
        ]);

        // Simular paginación
        $elecciones = new \Illuminate\Pagination\LengthAwarePaginator(
            $elecciones,
            $elecciones->count(),
            10,
            1,
            ['path' => request()->url()]
        );
        
        return view('votante.elecciones.index', compact('elecciones'));
    }

    /**
     * Lista de candidatos para votar - CON DATOS DE PRUEBA
     */
    public function listarCandidatos($eleccionId)
    {
        // Elección de prueba
        $eleccion = (object)[
            'id' => $eleccionId,
            'nombreEleccion' => 'Elecciones Estudiantiles 2026',
            'descripcion' => 'Elecciones para renovar el Consejo Estudiantil del periodo 2026',
            'fechaInicio' => now()->subDays(2),
            'fechaFin' => now()->addDays(5),
            'estadoEleccionesId' => 1
        ];

        // Cargos de prueba
        $cargos = collect([
            (object)[
                'id' => 1,
                'nombreCargo' => 'Presidente',
                'descripcionCargo' => 'Presidente del Consejo Estudiantil'
            ],
            (object)[
                'id' => 2,
                'nombreCargo' => 'Vicepresidente',
                'descripcionCargo' => 'Vicepresidente del Consejo Estudiantil'
            ],
            (object)[
                'id' => 3,
                'nombreCargo' => 'Secretario',
                'descripcionCargo' => 'Secretario General'
            ]
        ]);

        // Candidatos de prueba agrupados por cargo
        $candidatosPorCargo = [
            1 => collect([
                (object)[
                    'id' => 1,
                    'cargoId' => 1,
                    'partidoId' => 1,
                    'biografia' => 'Estudiante comprometido con el cambio',
                    'usuario' => (object)[
                        'id' => 1,
                        'email' => 'candidato1@ejemplo.com',
                        'perfil' => (object)[
                            'nombres' => 'Juan Carlos',
                            'apellidoPaterno' => 'Pérez',
                            'apellidoMaterno' => 'García',
                            'fotoPerfil' => null,
                            'telefono' => '987654321',
                            'ciclo' => '8vo Ciclo',
                            'carrera' => (object)['nombreCarrera' => 'Ingeniería de Sistemas']
                        ]
                    ],
                    'partido' => (object)[
                        'id' => 1,
                        'nombrePartido' => 'Movimiento Estudiantil Progresista',
                        'siglas' => 'MEP',
                        'color1' => '#3B82F6',
                        'color2' => '#1E40AF',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Presidente'],
                    'propuestas' => collect([
                        (object)[
                            'titulo' => 'Mejora de infraestructura',
                            'descripcion' => 'Renovar las instalaciones deportivas y académicas'
                        ],
                        (object)[
                            'titulo' => 'Becas estudiantiles',
                            'descripcion' => 'Ampliar el programa de becas para estudiantes de bajos recursos'
                        ]
                    ])
                ],
                (object)[
                    'id' => 2,
                    'cargoId' => 1,
                    'partidoId' => 2,
                    'biografia' => 'Experiencia en liderazgo estudiantil',
                    'usuario' => (object)[
                        'id' => 2,
                        'email' => 'candidato2@ejemplo.com',
                        'perfil' => (object)[
                            'nombres' => 'María Elena',
                            'apellidoPaterno' => 'López',
                            'apellidoMaterno' => 'Martínez',
                            'fotoPerfil' => null,
                            'telefono' => '987654322',
                            'ciclo' => '7mo Ciclo',
                            'carrera' => (object)['nombreCarrera' => 'Administración']
                        ]
                    ],
                    'partido' => (object)[
                        'id' => 2,
                        'nombrePartido' => 'Frente Universitario Unido',
                        'siglas' => 'FUU',
                        'color1' => '#EF4444',
                        'color2' => '#B91C1C',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Presidente'],
                    'propuestas' => collect([
                        (object)[
                            'titulo' => 'Digitalización universitaria',
                            'descripcion' => 'Implementar plataformas digitales modernas'
                        ]
                    ])
                ],
                (object)[
                    'id' => 3,
                    'cargoId' => 1,
                    'partidoId' => 3,
                    'biografia' => 'Comprometido con la transparencia',
                    'usuario' => (object)[
                        'id' => 3,
                        'email' => 'candidato3@ejemplo.com',
                        'perfil' => (object)[
                            'nombres' => 'Roberto',
                            'apellidoPaterno' => 'Sánchez',
                            'apellidoMaterno' => 'Torres',
                            'fotoPerfil' => null,
                            'telefono' => '987654323',
                            'ciclo' => '9no Ciclo',
                            'carrera' => (object)['nombreCarrera' => 'Derecho']
                        ]
                    ],
                    'partido' => (object)[
                        'id' => 3,
                        'nombrePartido' => 'Alianza Estudiantil',
                        'siglas' => 'AE',
                        'color1' => '#10B981',
                        'color2' => '#047857',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Presidente'],
                    'propuestas' => collect([
                        (object)[
                            'titulo' => 'Participación estudiantil',
                            'descripcion' => 'Crear espacios de diálogo permanente'
                        ]
                    ])
                ]
            ]),
            2 => collect([
                (object)[
                    'id' => 4,
                    'cargoId' => 2,
                    'partidoId' => 1,
                    'biografia' => 'Experiencia en gestión',
                    'usuario' => (object)[
                        'id' => 4,
                        'email' => 'candidato4@ejemplo.com',
                        'perfil' => (object)[
                            'nombres' => 'Ana',
                            'apellidoPaterno' => 'Rodríguez',
                            'apellidoMaterno' => 'Vega',
                            'fotoPerfil' => null,
                            'telefono' => '987654324',
                            'ciclo' => '6to Ciclo',
                            'carrera' => (object)['nombreCarrera' => 'Contabilidad']
                        ]
                    ],
                    'partido' => (object)[
                        'id' => 1,
                        'nombrePartido' => 'Movimiento Estudiantil Progresista',
                        'siglas' => 'MEP',
                        'color1' => '#3B82F6',
                        'color2' => '#1E40AF',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Vicepresidente'],
                    'propuestas' => collect([])
                ],
                (object)[
                    'id' => 5,
                    'cargoId' => 2,
                    'partidoId' => 2,
                    'biografia' => 'Líder nato',
                    'usuario' => (object)[
                        'id' => 5,
                        'email' => 'candidato5@ejemplo.com',
                        'perfil' => (object)[
                            'nombres' => 'Carlos',
                            'apellidoPaterno' => 'Mendoza',
                            'apellidoMaterno' => 'Ríos',
                            'fotoPerfil' => null,
                            'telefono' => '987654325',
                            'ciclo' => '8vo Ciclo',
                            'carrera' => (object)['nombreCarrera' => 'Marketing']
                        ]
                    ],
                    'partido' => (object)[
                        'id' => 2,
                        'nombrePartido' => 'Frente Universitario Unido',
                        'siglas' => 'FUU',
                        'color1' => '#EF4444',
                        'color2' => '#B91C1C',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Vicepresidente'],
                    'propuestas' => collect([])
                ]
            ]),
            3 => collect([
                (object)[
                    'id' => 6,
                    'cargoId' => 3,
                    'partidoId' => 1,
                    'biografia' => 'Organizado y responsable',
                    'usuario' => (object)[
                        'id' => 6,
                        'email' => 'candidato6@ejemplo.com',
                        'perfil' => (object)[
                            'nombres' => 'Laura',
                            'apellidoPaterno' => 'Flores',
                            'apellidoMaterno' => 'Cruz',
                            'fotoPerfil' => null,
                            'telefono' => '987654326',
                            'ciclo' => '5to Ciclo',
                            'carrera' => (object)['nombreCarrera' => 'Comunicaciones']
                        ]
                    ],
                    'partido' => (object)[
                        'id' => 1,
                        'nombrePartido' => 'Movimiento Estudiantil Progresista',
                        'siglas' => 'MEP',
                        'color1' => '#3B82F6',
                        'color2' => '#1E40AF',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Secretario'],
                    'propuestas' => collect([])
                ]
            ])
        ];

        return view('votante.votar.lista', compact('eleccion', 'cargos', 'candidatosPorCargo'));
    }

    /**
     * Detalle de un candidato - CON DATOS DE PRUEBA
     */
    public function verDetalleCandidato($eleccionId, $candidatoId)
    {
        $eleccion = (object)[
            'id' => $eleccionId,
            'nombreEleccion' => 'Elecciones Estudiantiles 2026'
        ];
        
        $candidato = (object)[
            'id' => $candidatoId,
            'biografia' => 'Estudiante comprometido con el cambio y la mejora continua de nuestra institución. Con 3 años de experiencia en organizaciones estudiantiles.',
            'usuario' => (object)[
                'id' => 1,
                'email' => 'candidato@ejemplo.com',
                'perfil' => (object)[
                    'nombres' => 'Juan Carlos',
                    'apellidoPaterno' => 'Pérez',
                    'apellidoMaterno' => 'García',
                    'fotoPerfil' => null,
                    'telefono' => '987654321',
                    'ciclo' => '8vo Ciclo',
                    'carrera' => (object)['nombreCarrera' => 'Ingeniería de Sistemas']
                ]
            ],
            'partido' => (object)[
                'id' => 1,
                'nombrePartido' => 'Movimiento Estudiantil Progresista',
                'siglas' => 'MEP',
                'color1' => '#3B82F6',
                'color2' => '#1E40AF',
                'logo' => null,
                'propuestas' => collect([
                    (object)[
                        'titulo' => 'Transparencia total',
                        'descripcion' => 'Rendición de cuentas mensual'
                    ]
                ])
            ],
            'cargo' => (object)['nombreCargo' => 'Presidente'],
            'propuestas' => collect([
                (object)[
                    'titulo' => 'Mejora de infraestructura',
                    'descripcion' => 'Renovar las instalaciones deportivas y académicas para brindar un mejor ambiente de estudio'
                ],
                (object)[
                    'titulo' => 'Becas estudiantiles',
                    'descripcion' => 'Ampliar el programa de becas para estudiantes de bajos recursos económicos'
                ],
                (object)[
                    'titulo' => 'Programa de tutorías',
                    'descripcion' => 'Implementar un sistema de tutorías entre estudiantes de ciclos superiores e inferiores'
                ]
            ])
        ];
        
        return view('votante.votar.detalle_candidato', compact('eleccion', 'candidato'));
    }

    /**
     * Procesa y emite el voto - SIMULADO
     */
    public function emitirVoto(Request $request, $eleccionId)
    {
        $eleccion = (object)[
            'id' => $eleccionId,
            'nombreEleccion' => 'Elecciones Estudiantiles 2026',
            'descripcion' => 'Elecciones para renovar el Consejo Estudiantil'
        ];

        // Simular votos registrados
        $votos = collect([
            (object)[
                'fechaVoto' => now(),
                'candidato' => (object)[
                    'usuario' => (object)[
                        'perfil' => (object)[
                            'nombres' => 'Juan Carlos',
                            'apellidoPaterno' => 'Pérez',
                            'fotoPerfil' => null,
                            'carrera' => (object)['nombreCarrera' => 'Ingeniería de Sistemas']
                        ]
                    ],
                    'partido' => (object)[
                        'nombrePartido' => 'Movimiento Estudiantil Progresista',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Presidente']
                ]
            ],
            (object)[
                'fechaVoto' => now(),
                'candidato' => (object)[
                    'usuario' => (object)[
                        'perfil' => (object)[
                            'nombres' => 'Ana',
                            'apellidoPaterno' => 'Rodríguez',
                            'fotoPerfil' => null,
                            'carrera' => (object)['nombreCarrera' => 'Contabilidad']
                        ]
                    ],
                    'partido' => (object)[
                        'nombrePartido' => 'Movimiento Estudiantil Progresista',
                        'logo' => null
                    ],
                    'cargo' => (object)['nombreCargo' => 'Vicepresidente']
                ]
            ]
        ]);

        return view('votante.votar.exito', compact('eleccion', 'votos'));
    }

    /**
     * Detalle de elección - CON DATOS DE PRUEBA
     */
    public function verDetalleEleccion($id)
    {
        $eleccion = (object)[
            'id' => $id,
            'nombreEleccion' => 'Elecciones Estudiantiles 2026',
            'descripcion' => 'Elecciones para renovar el Consejo Estudiantil',
            'fechaInicio' => now()->subDays(2),
            'fechaFin' => now()->addDays(5),
            'estadoEleccionesId' => 1,
            'estadoEleccion' => (object)['nombre' => 'Activo']
        ];

        $candidatos = collect([
            (object)[
                'id' => 1,
                'usuario' => (object)[
                    'perfil' => (object)[
                        'nombres' => 'Juan Carlos',
                        'apellidoPaterno' => 'Pérez',
                        'carrera' => (object)['nombreCarrera' => 'Ingeniería de Sistemas']
                    ]
                ],
                'partido' => (object)['nombrePartido' => 'MEP'],
                'cargo' => (object)['nombreCargo' => 'Presidente']
            ]
        ]);

        $yaVoto = false;

        return view('votante.elecciones.detalle', compact('eleccion', 'candidatos', 'yaVoto'));
    }
}