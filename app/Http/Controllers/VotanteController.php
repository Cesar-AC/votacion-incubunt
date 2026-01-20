<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\Voto;

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
     * Ver detalle de una elección - CON DATOS DE PRUEBA
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

        // Cuando conectes con BD, descomentar:
        /*
        $eleccion = Elecciones::with(['estadoEleccion', 'candidatos.usuario.perfil'])
            ->findOrFail($id);
        
        $yaVoto = Voto::where('idPadronElectoral', function($query) use ($id) {
            $query->select('id')
                  ->from('padron_electoral')
                  ->where('idUser', Auth::id())
                  ->where('idEleccion', $id)
                  ->limit(1);
        })->exists();
        
        $candidatos = $eleccion->candidatos;
        */

        return view('votante.elecciones.detalle', compact('eleccion', 'candidatos', 'yaVoto'));
    }

    /**
     * Iniciar proceso de votación
     */
    public function iniciarVotacion($eleccionId)
    {
        // Cuando conectes con BD, descomentar:
        /*
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar que la elección esté activa
        if (!method_exists($eleccion, 'estaActivo') || !$eleccion->estaActivo()) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'Esta elección no está activa.');
        }

        // Verificar que el usuario esté en el padrón electoral
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->first();

        if (!$padron) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'No estás registrado en el padrón electoral para esta elección.');
        }

        // Verificar si ya votó
        $yaVoto = Voto::where('idPadronElectoral', $padron->id)->exists();

        if ($yaVoto) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('info', 'Ya has emitido tu voto en esta elección.');
        }
        */

        return redirect()->route('votante.votar.lista', $eleccionId);
    }

    /**
     * Listar candidatos para votar - CON DATOS DE PRUEBA
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

        // Cuando conectes con BD, descomentar:
        /*
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar estado
        if (!method_exists($eleccion, 'estaActivo') || !$eleccion->estaActivo()) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'Esta elección no está activa.');
        }

        // Verificar padrón
        $padron = PadronElectoral::where('idUser', Auth::id())
            ->where('idEleccion', $eleccionId)
            ->first();

        if (!$padron) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'No estás registrado en el padrón electoral.');
        }

        // Obtener cargos y candidatos
        $cargos = Cargo::whereHas('candidatos', function($query) use ($eleccionId) {
            $query->where('idEleccion', $eleccionId);
        })->get();

        $candidatosPorCargo = [];
        foreach ($cargos as $cargo) {
            $candidatosPorCargo[$cargo->id] = Candidato::with([
                'usuario.perfil.carrera',
                'partido',
                'cargo',
                'propuestas'
            ])
            ->where('idCargo', $cargo->id)
            ->where('idEleccion', $eleccionId)
            ->get();
        }
        */

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

        // Cuando conectes con BD, descomentar:
        /*
        $eleccion = Elecciones::findOrFail($eleccionId);
        
        $candidato = Candidato::with([
            'usuario.perfil.carrera',
            'partido.propuestas',
            'cargo',
            'propuestas'
        ])
        ->where('idEleccion', $eleccionId)
        ->findOrFail($candidatoId);
        */
        
        return view('votante.votar.detalle_candidato', compact('eleccion', 'candidato'));
    }

    /**
     * Procesa y emite el voto
     */
    public function emitirVoto(Request $request, $eleccionId)
    {
        // SIMULACIÓN - Cuando conectes con BD, reemplazar todo esto:
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
            ]
        ]);

        // Redirigir a página de éxito
        return redirect()->route('votante.votar.exito', $eleccionId)
            ->with('success', '¡Tu voto ha sido registrado exitosamente!');

        // CÓDIGO REAL - Descomentar cuando conectes con BD:
        /*
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar estado
        if (!method_exists($eleccion, 'estaActivo') || !$eleccion->estaActivo()) {
            return back()->with('error', 'Esta elección no está activa.');
        }

        // Obtener padrón
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->firstOrFail();

        // Verificar si ya votó
        if (Voto::where('idPadronElectoral', $padron->id)->exists()) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('error', 'Ya has votado en esta elección.');
        }

        // Validar datos
        $request->validate([
            'candidatos' => 'required|array',
            'candidatos.*' => 'required|exists:candidatos,id'
        ]);

        try {
            DB::beginTransaction();

            // Registrar votos
            foreach ($request->candidatos as $cargoId => $candidatoId) {
                Voto::create([
                    'idCandidato' => $candidatoId,
                    'idPadronElectoral' => $padron->id,
                    'fechaVoto' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('votante.votar.exito', $eleccionId)
                ->with('success', '¡Tu voto ha sido registrado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Hubo un error al registrar tu voto. Por favor, intenta nuevamente.');
        }
        */
    }

    /**
     * Pantalla de éxito después de votar
     */
    public function votoExitoso($eleccionId)
    {
        // SIMULACIÓN - Datos de prueba
        $eleccion = (object)[
            'id' => $eleccionId,
            'nombreEleccion' => 'Elecciones Estudiantiles 2026',
            'descripcion' => 'Elecciones para renovar el Consejo Estudiantil'
        ];

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

        // CÓDIGO REAL - Descomentar cuando conectes con BD:
        /*
        $eleccion = Elecciones::findOrFail($eleccionId);
        
        // Obtener el padrón y votos del usuario
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->firstOrFail();

        $votos = Voto::with(['candidato.usuario.perfil', 'candidato.partido', 'candidato.cargo'])
            ->where('idPadronElectoral', $padron->id)
            ->get();
        */

        return view('votante.votar.exito', compact('eleccion', 'votos'));
    }
}