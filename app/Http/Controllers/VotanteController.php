<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\Voto;
use App\Models\Partido;
use App\Models\Candidato;
use App\Models\PropuestaPartido;
use App\Models\PropuestaCandidato;
use App\Interfaces\Services\IPermisoService;
use App\Enum\Permiso as PermisoEnum;
use App\Http\Requests\VotarRequest;
use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\ICarreraService;
use App\Interfaces\Services\ICandidatoService;
use App\Interfaces\Services\ICargoService;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use App\Interfaces\Services\IUserService;
use App\Interfaces\Services\IVotoService;
use App\Models\CandidatoEleccion;
use App\Models\PartidoEleccion;
use App\Models\Area;

class VotanteController extends Controller
{
    public function __construct(
        protected IEleccionesService $eleccionesService,
        protected IPartidoService $partidoService,
        protected IPermisoService $permisoService,
        protected IVotoService $votoService
    ) {}
    /**
     * Página principal del votante
     */
    public function home()
    {
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();

        try {
            $eleccionActiva = $this->eleccionesService->estaEnPadronElectoral(Auth::user()) ? $eleccionActiva : null;
            $esPeriodoDeVotar = $this->eleccionesService->votacionHabilitada($eleccionActiva);
            $yaVotoUsuario = $this->votoService->haVotado(Auth::user(), $eleccionActiva);
        } catch (\Exception $e) {
            $eleccionActiva = null;
            $esPeriodoDeVotar = false;
            $yaVotoUsuario = false;
        }

        return view('votante.home', compact('eleccionActiva', 'esPeriodoDeVotar', 'yaVotoUsuario'));
    }

    /**
     * Editar perfil del votante
     */
    public function editarPerfil(IAreaService $areaService, ICarreraService $carreraService)
    {
        $permiso = $this->permisoService->permisoDesdeEnum(PermisoEnum::PERFIL_EDITAR);

        if (!$permiso || !$this->permisoService->comprobarUsuario(Auth::user(), $permiso)) {
            return redirect()->route('profile.show')->withErrors(['permiso' => 'No tienes permiso para editar tu perfil.']);
        }

        $user = Auth::user()->load(['perfil.carrera', 'perfil.area']);
        $areas = $areaService->obtenerAreas();
        $carreras = $carreraService->obtenerCarreras();
        $candidato = Candidato::where('idUsuario', Auth::id())->first();
        $partido = $this->obtenerPartidoDeCandidato($candidato);
        $puedeEditarPartido = $partido ? Auth::user()->can('update', $partido) : false;

        return view('votante.perfil.editar', compact('user', 'candidato', 'partido', 'puedeEditarPartido', 'areas', 'carreras'));
    }

    /**
     * Actualizar perfil del votante
     */
    public function actualizarPerfil(Request $request, IUserService $userService, IPartidoService $partidoService)
    {
        $permiso = $this->permisoService->permisoDesdeEnum(PermisoEnum::PERFIL_EDITAR);

        if (!$permiso || !$this->permisoService->comprobarUsuario(Auth::user(), $permiso)) {
            return redirect()->route('profile.show')->withErrors(['permiso' => 'No tienes permiso para editar tu perfil.']);
        }

        $user = Auth::user();

        $perfilData = $request->validate([
            'apellidoPaterno' => 'required|string|max:20',
            'apellidoMaterno' => 'required|string|max:20',
            'nombre' => 'required|string|max:20',
            'otrosNombres' => 'nullable|string|max:40',
            'dni' => 'required|string|max:8',
            'telefono' => 'nullable|string|max:20',
            'idCarrera' => 'required|integer|exists:Carrera,idCarrera',
            'idArea' => 'required|integer|exists:Area,idArea',
        ], [
            'apellidoPaterno.required' => 'El apellido paterno es obligatorio.',
            'apellidoMaterno.required' => 'El apellido materno es obligatorio.',
            'nombre.required' => 'El nombre es obligatorio.',
            'dni.required' => 'El DNI es obligatorio.',
            'idCarrera.required' => 'La carrera es obligatoria.',
            'idCarrera.exists' => 'La carrera no es valida.',
            'idArea.required' => 'El area es obligatoria.',
            'idArea.exists' => 'El area no es valida.',
        ]);

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'foto.image' => 'La foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser de tipo: jpeg, png, jpg, gif.',
            'foto.max' => 'La foto no puede exceder los 5MB.',
        ]);

        try {
            if ($user->perfil) {
                $userService->editarPerfilUsuario($perfilData, $user);
            } else {
                $perfilData['idUser'] = $user->getKey();
                \App\Models\PerfilUsuario::create($perfilData);
            }

            if ($request->hasFile('foto')) {
                $userService->cambiarFoto($user, $request->file('foto'));
            }

            $candidato = Candidato::where('idUsuario', Auth::id())->first();
            $request->validate([
                'planTrabajoCandidato' => 'nullable|url|max:1000',
            ], [
                'planTrabajoCandidato.url' => 'El plan de trabajo del candidato debe ser una URL valida.',
            ]);

            if ($candidato) {
                $candidato->update([
                    'planTrabajo' => $request->input('planTrabajoCandidato'),
                ]);
            }

            $partido = $this->obtenerPartidoDeCandidato($candidato);
            $intentaEditarPartido = $partido && (
                $request->filled('partido_urlPartido') ||
                $request->filled('partido_descripcion') ||
                $request->filled('partido_planTrabajo') ||
                $request->filled('partido_tipo') ||
                $request->hasFile('partido_foto')
            );

            if ($intentaEditarPartido) {
                $this->authorize('update', $partido);

                $request->validate([
                    'partido_urlPartido' => 'nullable|url|max:255',
                    'partido_descripcion' => 'nullable|string',
                    'partido_planTrabajo' => 'nullable|url|max:1000',
                    'partido_tipo' => 'nullable|string|max:255',
                    'partido_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                ], [
                    'partido_urlPartido.url' => 'La URL del partido debe ser valida.',
                    'partido_planTrabajo.url' => 'El plan de trabajo del partido debe ser una URL valida.',
                ]);

                $partidoData = array_filter([
                    'urlPartido' => $request->input('partido_urlPartido'),
                    'descripcion' => $request->input('partido_descripcion'),
                    'planTrabajo' => $request->input('partido_planTrabajo'),
                    'tipo' => $request->input('partido_tipo'),
                ], function ($value) {
                    return $value !== null;
                });

                if (!empty($partidoData)) {
                    $partidoService->editarPartido($partidoData, $partido);
                }

                if ($request->hasFile('partido_foto')) {
                    $partidoService->cambiarFotoPartido($partido, $request->file('partido_foto'));
                }
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error_general' => 'Error al guardar los cambios: ' . $e->getMessage()]);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    private function obtenerPartidoDeCandidato(?Candidato $candidato): ?Partido
    {
        if (!$candidato) {
            return null;
        }

        $candidatoEleccion = CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->whereNotNull('idPartido')
            ->orderByDesc('idElecciones')
            ->first();

        if (!$candidatoEleccion) {
            return null;
        }

        return Partido::find($candidatoEleccion->idPartido);
    }

    /**
     * Ver propuestas (Candidatos y Partidos)
     */
    public function propuestas(IAreaService $areaService, ICandidatoService $candidatoService)
    {
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();
        $partidos = $eleccionActiva->partidos;
        $areas = $areaService->obtenerAreas();

        // Obtener TODOS los candidatos de la elección (similar a la página de votación)
        $candidatosPorArea = CandidatoEleccion::query()
            ->where('idElecciones', '=', $eleccionActiva->getKey())
            ->with([
                'candidato.usuario.perfil',
                'candidato.propuestas' => function ($q) use ($eleccionActiva) {
                    $q->where('idElecciones', '=', $eleccionActiva->getKey());
                },
                'cargo.area',
            ])
            ->get();

        return view('votante.propuestas.index', compact('eleccionActiva', 'partidos', 'areas', 'candidatosPorArea', 'candidatoService'));
    }

    /**
     * Lista todas las elecciones
     */
    public function listarElecciones()
    {
        $elecciones = Elecciones::with('estadoEleccion')
            ->orderBy('fechaInicio', 'desc')
            ->paginate(9);

        return view('votante.elecciones.index', compact('elecciones'));
    }

    /**
     * Ver detalle de una elección
     */
    public function verDetalleEleccion(int $id)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

        $yaVoto = $this->votoService->haVotado(Auth::user(), $eleccion);

        return view('votante.elecciones.detalle', compact('eleccion', 'yaVoto'));
    }

    /**
     * Iniciar proceso de votación
     */
    public function iniciarVotacion(int $eleccionId)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccionId);

        // Verificar que la elección esté activa
        if (!$eleccion->estaActivo()) {
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
        $yaVoto = $this->votoService->haVotado(Auth::user(), $eleccion);

        if ($yaVoto) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('info', 'Ya has emitido tu voto en esta elección.');
        }

        return redirect()->route('votante.votar.lista', $eleccionId);
    }

    /**
     * Listar candidatos para votar
     */
    public function vistaVotar(IAreaService $areaService, ICargoService $cargoService, ICandidatoService $candidatoService)
    {
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();
        $eleccionesService = $this->eleccionesService;

        if (!$eleccionActiva) {
            return redirect()->route('votante.home')->with('error', 'No hay elección activa');
        }

        $areas = $areaService->obtenerAreas();

        $partidos = $this->partidoService->obtenerPartidosInscritosEnEleccion($eleccionActiva);

        return view('votante.votar.lista', compact('eleccionActiva', 'areas', 'partidos', 'cargoService', 'candidatoService', 'eleccionesService'));
    }

    /**
     * Detalle de un candidato
     */
    public function verDetalleCandidato($eleccionId, $candidatoId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

        $candidato = \App\Models\Candidato::with([
            'usuario.perfil.carrera',
            'partido.propuestas',
            'cargo',
            'propuestas'
        ])
            ->whereHas('elecciones', function ($q) use ($eleccionId) {
                $q->where('Elecciones.idElecciones', $eleccionId);
            })
            ->findOrFail($candidatoId);

        return view('votante.votar.detalle_candidato', compact('eleccion', 'candidato'));
    }

    /**
     * Procesa y emite el voto
     */
    public function emitirVoto(VotarRequest $request, IPartidoService $partidoService, ICandidatoService $candidatoService)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionActiva();

        $validated = $request->safe();

        $entidades = [];
        $votosData = [];

        try {
            // Obtener y validar partido
            if (!$validated['idPartido']) {
                throw new \Exception('Debe seleccionar un partido político');
            }

            $partido = $partidoService->obtenerPartidoPorId($validated['idPartido']);
            if (!$partido) {
                throw new \Exception('El partido seleccionado no existe');
            }

            $entidades[] = $partido;
            $votosData['partido'] = [
                'id' => $partido->idPartido,
                'nombre' => $partido->partido,
                'color1' => $partido->color1 ?? '#3b82f6',
                'color2' => $partido->color2 ?? null,
            ];

            // Obtener y validar candidatos
            if (empty($validated['candidatos'])) {
                throw new \Exception('Debe seleccionar al menos un candidato');
            }

            $votosData['candidatos'] = [];
            foreach ($validated['candidatos'] as $candidatoId) {
                $candidato = $candidatoService->obtenerCandidatoPorId($candidatoId);
                if (!$candidato) {
                    throw new \Exception("El candidato con ID {$candidatoId} no existe");
                }

                // Cargar relación perfil del usuario
                $candidato->load('usuario.perfil');

                $entidades[] = $candidato;

                $candidatoEleccion = $candidato->candidatoElecciones()
                    ->where('idElecciones', $eleccion->idElecciones)
                    ->with('cargo', 'partido')
                    ->first();

                // Obtener nombre completo del perfil del usuario
                $nombreCompleto = 'Candidato';
                if ($candidato->usuario?->perfil) {
                    $nombreCompleto = trim(
                        ($candidato->usuario->perfil->nombre ?? '') . ' ' . 
                        ($candidato->usuario->perfil->apellidoPaterno ?? '') . ' ' .
                        ($candidato->usuario->perfil->apellidoMaterno ?? '')
                    );
                    if (empty($nombreCompleto)) {
                        $nombreCompleto = 'Candidato';
                    }
                }

                $votosData['candidatos'][] = [
                    'id' => $candidato->idCandidato,
                    'nombre' => $nombreCompleto,
                    'cargo' => $candidatoEleccion?->cargo?->cargo ?? 'Sin cargo',
                    'partido' => $candidatoEleccion?->partido?->partido ?? 'Independiente',
                ];
            }

            // Emitir voto
            $this->votoService->votar(Auth::user(), collect($entidades));

            // Guardar datos en la sesión para la vista de éxito
            session(['votos_emitidos' => $votosData]);

            return redirect()->route('votante.votar.exito', $eleccion->idElecciones);
        } catch (\Exception $e) {
            \Log::error('Error al emitir voto: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'eleccion_id' => $eleccion->idElecciones,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('votante.votar.lista', $eleccion->idElecciones)
                ->withErrors(['vote_error' => '❌ Error al procesar tu voto: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Pantalla de éxito después de votar
     */
    public function votoExitoso($eleccionId)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccionId);
        $user = Auth::user();

        // Verificar que el usuario ha votado en esta elección
        if (!$this->votoService->haVotado($user, $eleccion)) {
            return redirect()->route('votante.home')
                ->withErrors('No hay registro de voto para esta elección.');
        }

        // Obtener datos de votos de la sesión
        $votosData = session('votos_emitidos', [
            'partido' => null,
            'candidatos' => []
        ]);

        // Preparar datos para la vista
        $votosPartido = [];
        if ($votosData['partido'] ?? null) {
            $votosPartido[] = (object)$votosData['partido'];
        }

        $votosCandidato = [];
        foreach ($votosData['candidatos'] ?? [] as $candidatoData) {
            $votosCandidato[] = (object)$candidatoData;
        }

        return view('votante.votar.exito', compact('eleccion', 'votosPartido', 'votosCandidato'));
    }

    /**
     * Generar comprobante de voto en PDF
     */
    public function generarComprobantePDF($eleccionId)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccionId);
        $user = Auth::user();

        // Verificar que el usuario ha votado
        if (!$this->votoService->haVotado($user, $eleccion)) {
            return redirect()->route('votante.home')->withErrors('No has votado en esta elección.');
        }

        // Obtener datos de votos de la sesión
        $votosData = session('votos_emitidos', [
            'partido' => null,
            'candidatos' => []
        ]);

        // Preparar datos para el PDF
        $votosPartido = [];
        if ($votosData['partido'] ?? null) {
            $votosPartido[] = (object)$votosData['partido'];
        }

        $votosCandidato = [];
        foreach ($votosData['candidatos'] ?? [] as $candidatoData) {
            $votosCandidato[] = (object)$candidatoData;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('votante.votar.comprobante-pdf', compact('eleccion', 'votosPartido', 'votosCandidato', 'user'));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('comprobante_voto_' . $eleccionId . '_' . $user->id . '.pdf');
    }

    // =============================================
    // GESTIÓN DE PROPUESTAS DE PARTIDO
    // =============================================

    /**
     * Listar mis propuestas de partido
     */
    public function misPropuestasPartido()
    {
        // Verificar que el usuario tenga permiso usando el servicio
        $permisoCandidato = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_CANDIDATO_CRUD);
        $permisoPartido = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_PARTIDO_CRUD);

        $tienePermiso = $this->permisoService->comprobarUsuario(Auth::user(), $permisoCandidato)
            || $this->permisoService->comprobarUsuario(Auth::user(), $permisoPartido);

        if (!$tienePermiso) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Verificar si el usuario es candidato y obtener su partido
        $candidato = Candidato::where('idUsuario', Auth::id())->first();
        $partido = $this->obtenerPartidoDeCandidato($candidato);

        if (!$partido) {
            return view('votante.propuestas_partido.index', [
                'propuestas' => collect([]),
                'mensaje' => 'No perteneces a ningún partido político. Solo los candidatos afiliados a un partido pueden gestionar propuestas de partido.'
            ]);
        }

        $propuestas = PropuestaPartido::where('idPartido', $partido->getKey())
            ->orderBy('idPropuesta', 'desc')
            ->get();

        return view('votante.propuestas_partido.index', compact('propuestas', 'partido'));
    }

    /**
     * Formulario para crear propuesta de partido
     */
    public function crearPropuestaPartido()
    {
        // Verificar que el usuario tenga permiso usando el servicio
        $permisoCandidato = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_CANDIDATO_CRUD);
        $permisoPartido = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_PARTIDO_CRUD);

        $tienePermiso = $this->permisoService->comprobarUsuario(Auth::user(), $permisoCandidato)
            || $this->permisoService->comprobarUsuario(Auth::user(), $permisoPartido);

        if (!$tienePermiso) {
            abort(403, 'No tienes permiso para crear propuestas de partido.');
        }

        // Verificar que pertenezca a un partido
        $candidato = Candidato::where('idUsuario', Auth::id())->first();
        $partido = $this->obtenerPartidoDeCandidato($candidato);

        if (!$partido) {
            return redirect()->route('votante.propuestas_partido.index')
                ->with('error', 'No perteneces a ningún partido político.');
        }

        return view('votante.propuestas_partido.crear', compact('partido'));
    }

    /**
     * Guardar nueva propuesta de partido
     */
    public function guardarPropuestaPartido(Request $request)
    {
        // Verificar que el usuario tenga permiso usando el servicio
        $permisoCandidato = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_CANDIDATO_CRUD);
        $permisoPartido = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_PARTIDO_CRUD);

        $tienePermiso = $this->permisoService->comprobarUsuario(Auth::user(), $permisoCandidato)
            || $this->permisoService->comprobarUsuario(Auth::user(), $permisoPartido);

        if (!$tienePermiso) {
            abort(403, 'No tiene permisos para guardar propuestas de partido.');
        }

        // Verificar que pertenezca a un partido
        $candidato = Candidato::where('idUsuario', Auth::id())->first();
        $partido = $this->obtenerPartidoDeCandidato($candidato);

        if (!$partido) {
            return redirect()->route('votante.propuestas_partido.index')
                ->with('error', 'No perteneces a ningún partido político.');
        }

        $request->validate([
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000'
        ]);

        try {
            PropuestaPartido::create([
                'propuesta' => $request->propuesta,
                'descripcion' => $request->descripcion,
                'idPartido' => $partido->getKey()
            ]);

            return redirect()->route('votante.propuestas_partido.index')
                ->with('success', 'Propuesta de partido creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para editar propuesta de partido
     */
    public function editarPropuestaPartido($id)
    {
        $propuesta = PropuestaPartido::findOrFail($id);

        // Verificar que el usuario tenga permiso usando el servicio
        $permisoCandidato = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_CANDIDATO_CRUD);
        $permisoPartido = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_PARTIDO_CRUD);

        $tienePermiso = $this->permisoService->comprobarUsuario(Auth::user(), $permisoCandidato)
            || $this->permisoService->comprobarUsuario(Auth::user(), $permisoPartido);

        if (!$tienePermiso) {
            abort(403, 'No tiene permisos para editar propuestas de partido.');
        }

        // Verificar que la propuesta pertenezca al partido del usuario
        $candidato = Candidato::where('idUsuario', Auth::id())->first();
        $partido = $this->obtenerPartidoDeCandidato($candidato);

        if (!$partido || $propuesta->idPartido != $partido->getKey()) {
            abort(403, 'Solo puedes editar propuestas de tu partido.');
        }

        return view('votante.propuestas_partido.editar', compact('propuesta', 'partido'));
    }

    /**
     * Actualizar propuesta de partido
     */
    public function actualizarPropuestaPartido(Request $request, $id)
    {
        $propuesta = PropuestaPartido::findOrFail($id);

        // Verificar que el usuario tenga permiso usando el servicio
        $permisoCandidato = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_CANDIDATO_CRUD);
        $permisoPartido = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_PARTIDO_CRUD);

        $tienePermiso = $this->permisoService->comprobarUsuario(Auth::user(), $permisoCandidato)
            || $this->permisoService->comprobarUsuario(Auth::user(), $permisoPartido);

        if (!$tienePermiso) {
            abort(403, 'No tiene permisos para actualizar propuestas de partido.');
        }

        // Verificar que la propuesta pertenezca al partido del usuario
        $candidato = Candidato::where('idUsuario', Auth::id())->first();
        $partido = $this->obtenerPartidoDeCandidato($candidato);

        if (!$partido || $propuesta->idPartido != $partido->getKey()) {
            abort(403, 'Solo puedes editar propuestas de tu partido.');
        }

        $request->validate([
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000'
        ]);

        try {
            $propuesta->update([
                'propuesta' => $request->propuesta,
                'descripcion' => $request->descripcion
            ]);

            return redirect()->route('votante.propuestas_partido.index')
                ->with('success', 'Propuesta de partido actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar propuesta de partido
     */
    public function eliminarPropuestaPartido($id)
    {
        $propuesta = PropuestaPartido::findOrFail($id);

        // Verificar que el usuario tenga permiso usando el servicio
        $permisoCandidato = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_CANDIDATO_CRUD);
        $permisoPartido = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_PARTIDO_CRUD);

        $tienePermiso = $this->permisoService->comprobarUsuario(Auth::user(), $permisoCandidato)
            || $this->permisoService->comprobarUsuario(Auth::user(), $permisoPartido);

        if (!$tienePermiso) {
            abort(403, 'No tiene permisos para eliminar propuestas de partido.');
        }

        // Verificar que la propuesta pertenezca al partido del usuario
        $candidato = Candidato::where('idUsuario', Auth::id())->first();
        $partido = $this->obtenerPartidoDeCandidato($candidato);

        if (!$partido || $propuesta->idPartido != $partido->getKey()) {
            abort(403, 'Solo puedes eliminar propuestas de tu partido.');
        }

        try {
            $propuesta->delete();

            return redirect()->route('votante.propuestas_partido.index')
                ->with('success', 'Propuesta de partido eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la propuesta: ' . $e->getMessage());
        }
    }

    // =============================================
    // GESTIÓN DE PROPUESTAS DE CANDIDATO
    // =============================================

    /**
     * Listar mis propuestas como candidato
     */
    public function misPropuestasCandidato()
    {
        // Verificar que el usuario tenga permiso usando el servicio
        $permisoCandidato = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_CANDIDATO_CRUD);
        $permisoPartido = $this->permisoService->permisoDesdeEnum(PermisoEnum::PROPUESTA_PARTIDO_CRUD);

        $tienePermiso = $this->permisoService->comprobarUsuario(Auth::user(), $permisoCandidato)
            || $this->permisoService->comprobarUsuario(Auth::user(), $permisoPartido);

        if (!$tienePermiso) {
            abort(403, 'No tiene permisos para gestionar propuestas de candidato.');
        }

        // Verificar si el usuario es candidato en alguna elección
        $candidato = Candidato::where('idUsuario', Auth::id())
            ->with('usuario.perfil', 'candidatoElecciones.cargo', 'candidatoElecciones.partido')
            ->first();

        if (!$candidato) {
            return view('votante.propuestas_candidato.index', [
                'propuestas' => collect([]),
                'mensaje' => 'No estás registrado como candidato en ninguna elección. Solo los candidatos pueden gestionar sus propuestas.'
            ]);
        }

        // Verificar si está participando en alguna elección
        $elecciones = $candidato->elecciones;

        if ($elecciones->isEmpty()) {
            return view('votante.propuestas_candidato.index', [
                'propuestas' => collect([]),
                'candidato' => $candidato,
                'mensaje' => 'No estás participando en ninguna elección actualmente.'
            ]);
        }

        $propuestas = PropuestaCandidato::where('idCandidato', $candidato->idCandidato)
            ->orderBy('idPropuesta', 'desc')
            ->get();

        // Obtener cargo y partido de la primera candidatura
        $primeraCandidatura = $candidato->candidatoElecciones->first();
        $cargo = $primeraCandidatura?->cargo;
        $partido = $primeraCandidatura?->partido;

        return view('votante.propuestas_candidato.index', compact('propuestas', 'candidato', 'elecciones', 'cargo', 'partido'));
    }

    /**
     * Formulario para crear propuesta de candidato
     */
    public function crearPropuestaCandidato()
    {
        // Verificar que el usuario tenga permiso
        if (!Auth::user()->can('create', PropuestaCandidato::class)) {
            abort(403, 'No tienes permiso para crear propuestas de candidato.');
        }

        // Verificar que sea candidato
        $candidato = Candidato::where('idUsuario', Auth::id())->first();

        if (!$candidato) {
            return redirect()->route('votante.propuestas_candidato.index')
                ->with('error', 'No estás registrado como candidato.');
        }

        // Verificar si está participando en alguna elección
        $elecciones = $candidato->elecciones;

        if ($elecciones->isEmpty()) {
            return redirect()->route('votante.propuestas_candidato.index')
                ->with('error', 'No estás participando en ninguna elección actualmente.');
        }

        return view('votante.propuestas_candidato.crear', compact('candidato'));
    }

    /**
     * Guardar nueva propuesta de candidato
     */
    public function guardarPropuestaCandidato(Request $request)
    {
        // Verificar que el usuario tenga permiso
        if (!Auth::user()->can('create', PropuestaCandidato::class)) {
            abort(403, 'No tienes permiso para crear propuestas de candidato.');
        }

        // Verificar que sea candidato
        $candidato = Candidato::where('idUsuario', Auth::id())->first();

        if (!$candidato) {
            return redirect()->route('votante.propuestas_candidato.index')
                ->with('error', 'No estás registrado como candidato.');
        }

        $request->validate([
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000'
        ]);

        try {
            PropuestaCandidato::create([
                'propuesta' => $request->propuesta,
                'descripcion' => $request->descripcion,
                'idCandidato' => $candidato->idCandidato
            ]);

            return redirect()->route('votante.propuestas_candidato.index')
                ->with('success', 'Propuesta de candidato creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para editar propuesta de candidato
     */
    public function editarPropuestaCandidato($id)
    {
        $propuesta = PropuestaCandidato::findOrFail($id);

        // Verificar que el usuario tenga permiso
        if (!Auth::user()->can('update', $propuesta)) {
            abort(403, 'No tienes permiso para editar esta propuesta.');
        }

        // Verificar que la propuesta pertenezca al usuario
        $candidato = Candidato::where('idUsuario', Auth::id())
            ->with('usuario.perfil', 'cargo', 'partido')
            ->first();

        if (!$candidato || $propuesta->idCandidato != $candidato->idCandidato) {
            abort(403, 'Solo puedes editar tus propias propuestas.');
        }

        return view('votante.propuestas_candidato.editar', compact('propuesta', 'candidato'));
    }

    /**
     * Actualizar propuesta de candidato
     */
    public function actualizarPropuestaCandidato(Request $request, $id)
    {
        $propuesta = PropuestaCandidato::findOrFail($id);

        // Verificar que el usuario tenga permiso
        if (!Auth::user()->can('update', $propuesta)) {
            abort(403, 'No tienes permiso para editar esta propuesta.');
        }

        // Verificar que la propuesta pertenezca al usuario
        $candidato = Candidato::where('idUsuario', Auth::id())
            ->with('usuario.perfil', 'cargo', 'partido')
            ->first();

        if (!$candidato || $propuesta->idCandidato != $candidato->idCandidato) {
            abort(403, 'Solo puedes editar tus propias propuestas.');
        }

        $request->validate([
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000'
        ]);

        try {
            $propuesta->update([
                'propuesta' => $request->propuesta,
                'descripcion' => $request->descripcion
            ]);

            return redirect()->route('votante.propuestas_candidato.index')
                ->with('success', 'Propuesta de candidato actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar propuesta de candidato
     */
    public function eliminarPropuestaCandidato($id)
    {
        $propuesta = PropuestaCandidato::findOrFail($id);

        // Verificar que el usuario tenga permiso
        if (!Auth::user()->can('delete', $propuesta)) {
            abort(403, 'No tienes permiso para eliminar esta propuesta.');
        }

        // Verificar que la propuesta pertenezca al usuario
        $candidato = Candidato::where('idUsuario', Auth::id())->first();

        if (!$candidato || $propuesta->idCandidato != $candidato->idCandidato) {
            abort(403, 'Solo puedes eliminar tus propias propuestas.');
        }

        try {
            $propuesta->delete();

            return redirect()->route('votante.propuestas_candidato.index')
                ->with('success', 'Propuesta de candidato eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Obtener candidatos presidenciales de un partido (API JSON)
     */
    public function obtenerCandidatosPartido($partidoId)
    {
        $partido = Partido::findOrFail($partidoId);
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();

        $candidatos = CandidatoEleccion::query()
            ->where('idPartido', $partidoId)
            ->where('idElecciones', $eleccionActiva->getKey())
            ->with(['candidato.usuario.perfil', 'cargo'])
            ->get()
            ->map(function ($candidatoEleccion) {
                $perfil = $candidatoEleccion->candidato->usuario->perfil;
                $nombre = trim("{$perfil->nombre} {$perfil->otrosNombres} {$perfil->apellidoPaterno} {$perfil->apellidoMaterno}");
                $initials = strtoupper(substr($perfil->nombre, 0, 1) . substr($perfil->apellidoPaterno ?? '', 0, 1));

                return [
                    'id' => $candidatoEleccion->candidato->getKey(),
                    'nombre' => $nombre,
                    'initials' => $initials,
                    'cargo' => $candidatoEleccion->cargo->cargo,
                    'foto' => $perfil->obtenerFotoURL(),
                ];
            });

        return response()->json($candidatos);
    }

    /**
     * Obtener propuestas de un partido (API JSON)
     */
    public function obtenerPropuestasPartido($partidoId)
    {
        $partido = Partido::findOrFail($partidoId);
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();

        $propuestas = PropuestaPartido::query()
            ->where('idPartido', $partidoId)
            ->where('idElecciones', $eleccionActiva->getKey())
            ->get()
            ->map(function ($propuesta) {
                return [
                    'id' => $propuesta->getKey(),
                    'propuesta' => $propuesta->propuesta,
                    'descripcion' => $propuesta->descripcion ?? '',
                ];
            });

        return response()->json($propuestas);
    }

    /**
     * Obtener propuestas de un candidato (API JSON)
     */
    public function obtenerPropuestasCandidato($candidatoId)
    {
        $candidato = Candidato::findOrFail($candidatoId);
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();

        $propuestas = PropuestaCandidato::query()
            ->where('idCandidato', $candidatoId)
            ->where('idElecciones', $eleccionActiva->getKey())
            ->get()
            ->map(function ($propuesta) {
                return [
                    'id' => $propuesta->getKey(),
                    'propuesta' => $propuesta->propuesta ?? 'Propuesta',
                    'descripcion' => $propuesta->descripcion ?? '',
                ];
            });

        return response()->json($propuestas);
    }

    /**
     * Obtener propuestas del partido del candidato presidencial (API JSON)
     */
    public function obtenerPropuestasPartidoCandidato($candidatoId)
    {
        $candidato = Candidato::findOrFail($candidatoId);
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();

        // Obtener el partido del candidato
        $candidatoEleccion = CandidatoEleccion::query()
            ->where('idCandidato', $candidatoId)
            ->where('idElecciones', $eleccionActiva->getKey())
            ->whereNotNull('idPartido')
            ->first();

        if (!$candidatoEleccion || !$candidatoEleccion->idPartido) {
            return response()->json([]);
        }

        // Obtener propuestas del partido
        $propuestas = PropuestaPartido::query()
            ->where('idPartido', $candidatoEleccion->idPartido)
            ->where('idElecciones', $eleccionActiva->getKey())
            ->get()
            ->map(function ($propuesta) {
                return [
                    'id' => $propuesta->getKey(),
                    'propuesta' => $propuesta->propuesta,
                    'descripcion' => $propuesta->descripcion ?? '',
                ];
            });

        return response()->json($propuestas);
    }
}
