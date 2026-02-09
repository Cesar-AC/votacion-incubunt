<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\Voto;
use App\Models\Partido;
use App\Models\Candidato;
use App\Models\PropuestaPartido;
use App\Models\PropuestaCandidato;
use App\Interfaces\Services\IPermisoService;
use App\Enum\Permiso as PermisoEnum;
use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\ICarreraService;
use App\Interfaces\Services\ICandidatoService;
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
        } catch (\Exception $e) {
            $eleccionActiva = null;
            $esPeriodoDeVotar = false;
        }

        return view('votante.home', compact('eleccionActiva', 'esPeriodoDeVotar'));
    }

    /**
     * Editar perfil del votante
     */
    public function editarPerfil(IAreaService $areaService, ICarreraService $carreraService)
    {
        $permiso = $this->permisoService->permisoDesdeEnum(PermisoEnum::PERFIL_EDITAR);

        if (!$permiso || !$this->permisoService->comprobarUsuario(Auth::user(), $permiso)) {
            abort(403, 'No tienes permiso para editar tu perfil.');
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
            abort(403, 'No tienes permiso para editar tu perfil.');
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

        try {
            if ($user->perfil) {
                $userService->editarPerfilUsuario($perfilData, $user);
            } else {
                $perfilData['idUser'] = $user->getKey();
                \App\Models\PerfilUsuario::create($perfilData);
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

        $candidatosPorArea = CandidatoEleccion::query()
            ->where('idElecciones', '=', $eleccionActiva->getKey())
            ->whereNull('idPartido')
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
    public function vistaVotar(IAreaService $areaService)
    {
        $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();
        $areas = Area::where('idArea', '!=', Area::PRESIDENCIA)
            ->where('idArea', '!=', Area::SIN_AREA_ASIGNADA)
            ->with('cargos.candidatoElecciones.candidato.usuario.perfil')
            ->get();

        $partidos = PartidoEleccion::query()
            ->where('idElecciones', $eleccionActiva->getKey())
            ->with([
                'partido' => function ($q) {
                    $q->with([
                        'candidatos.usuario.perfil',
                        'propuestas' => function ($pq) {
                            $pq->whereNull('idElecciones')->orWhere('idElecciones', request()->route('eleccionId'));
                        }
                    ]);
                }
            ])
            ->get();

        return view('votante.votar.lista', compact('eleccionActiva', 'areas', 'partidos'));
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
    public function emitirVoto(Request $request, IPartidoService $partidoService, ICandidatoService $candidatoService)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionActiva();

        $request->validate(
            [
                'idPartido' => 'required|integer|exists:Partido,idPartido',
                'candidatos' => 'required|array',
                'candidatos.*' => 'required|integer|exists:Candidato,idCandidato',
            ],
            [
                'idPartido.required' => 'Se debe ingresar exactamente un partido.',
                'idPartido.integer' => 'El id del partido debe ser un número.',
                'idPartido.exists' => 'El partido no existe.',
                'candidatos.required' => 'Se debe ingresar al menos un candidato.',
                'candidatos.array' => 'Los candidatos deben ser una lista.',
                'candidatos.*.required' => 'Se debe ingresar al menos un candidato.',
                'candidatos.*.integer' => 'El id del candidato debe ser un número.',
                'candidatos.*.exists' => 'El candidato no existe.',
            ]
        );

        $entidades = [];

        $entidades[] = $partidoService->obtenerPartidoPorId($request->idPartido);

        foreach ($request->candidatos as $candidatoId) {
            $entidades[] = $candidatoService->obtenerCandidatoPorId($candidatoId);
        }

        try {
            $this->votoService->votar(Auth::user(), collect($entidades));
        } catch (\Exception $e) {
            return redirect()->route('votante.votar.lista', $eleccion->idElecciones)
                ->withErrors('Error al emitir el voto: ' . $e->getMessage());
        }

        return redirect()->route('votante.votar.exito', $eleccion->idElecciones);
    }

    /**
     * Pantalla de éxito después de votar
     */
    public function votoExitoso($eleccionId)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccionId);
        $user = Auth::user();
        // Obtener votos a partido
        $votosPartido = \App\Models\VotoPartido::where('idUsuario', $user->id)
            ->where('idElecciones', $eleccionId)
            ->with(['partido'])
            ->get();
        // Obtener votos a candidato
        $votosCandidato = \App\Models\VotoCandidato::where('idUsuario', $user->id)
            ->where('idElecciones', $eleccionId)
            ->with(['candidato.cargo'])
            ->get();
        return view('votante.votar.exito', compact('eleccion', 'votosPartido', 'votosCandidato'));
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

        if (!$candidato || !$candidato->idPartido) {
            return view('votante.propuestas_partido.index', [
                'propuestas' => collect([]),
                'mensaje' => 'No perteneces a ningún partido político. Solo los candidatos afiliados a un partido pueden gestionar propuestas de partido.'
            ]);
        }

        $partido = Partido::findOrFail($candidato->idPartido);
        $propuestas = PropuestaPartido::where('idPartido', $candidato->idPartido)
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

        if (!$candidato || !$candidato->idPartido) {
            return redirect()->route('votante.propuestas_partido.index')
                ->with('error', 'No perteneces a ningún partido político.');
        }

        $partido = Partido::findOrFail($candidato->idPartido);

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

        if (!$candidato || !$candidato->idPartido) {
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
                'idPartido' => $candidato->idPartido
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

        if (!$candidato || $propuesta->idPartido != $candidato->idPartido) {
            abort(403, 'Solo puedes editar propuestas de tu partido.');
        }

        $partido = Partido::findOrFail($candidato->idPartido);

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
        $candidato = Candidato::where('idUsuario', Auth::id())
            ->with('usuario.perfil', 'cargo', 'partido')
            ->first();

        if (!$candidato || $propuesta->idPartido != $candidato->idPartido) {
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

        if (!$candidato || $propuesta->idPartido != $candidato->idPartido) {
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
            ->with('usuario.perfil', 'cargo', 'partido')
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

        return view('votante.propuestas_candidato.index', compact('propuestas', 'candidato', 'elecciones'));
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
}
