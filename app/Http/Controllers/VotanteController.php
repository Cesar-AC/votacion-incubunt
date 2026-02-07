<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\Voto;
use App\Models\VotoPartido;
use App\Models\VotoCandidato;
use App\Models\Partido;
use App\Models\Area;
use App\Models\Candidato;
use App\Models\PropuestaPartido;
use App\Models\PropuestaCandidato;
use App\Interfaces\Services\IPermisoService;
use App\Enum\Permiso as PermisoEnum;
use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\ICandidatoService;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPadronElectoralService;
use App\Models\CandidatoEleccion;
use App\Models\PartidoEleccion;

class VotanteController extends Controller
{
    public function __construct(
        protected IEleccionesService $eleccionesService,
        protected IPermisoService $permisoService
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
    public function verDetalleEleccion($id)
    {
        $eleccion = Elecciones::with(['estadoEleccion', 'candidatos.usuario.perfil'])
            ->findOrFail($id);

        // Verificar si el usuario ya votó en esta elección
        // Primero obtener el registro del padrón electoral del usuario para esta elección
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $id)
            ->first();

        $yaVoto = false;
        if ($padron) {
            $yaVoto = Voto::where('idPadronElectoral', $padron->idPadronElectoral)->exists();
        }

        $candidatos = $eleccion->candidatos;

        return view('votante.elecciones.detalle', compact('eleccion', 'candidatos', 'yaVoto'));
    }

    /**
     * Iniciar proceso de votación
     */
    public function iniciarVotacion($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

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
        $yaVoto = Voto::where('idPadronElectoral', $padron->idPadronElectoral)->exists();

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

        $partidos = PartidoEleccion::query()
            ->where('idElecciones', $eleccionActiva->getKey())
            ->with([
                'partido.propuestas' => function ($q) use ($eleccionActiva) {
                    $q->where('idElecciones', $eleccionActiva->getKey());
                },
                'partido.candidatos.propuestas' => function ($q) use ($eleccionActiva) {
                    $q->where('idElecciones', $eleccionActiva->getKey());
                },
            ])
            ->get();

        return view('votante.votar.lista', compact('eleccion', 'cargos', 'candidatosPorCargo'));
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
    public function emitirVoto(Request $request, $eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar estado
        if (!$eleccion->estaActivo()) {
            return response()->json(['error' => 'Esta elección no está activa.'], 400);
        }

        // Obtener padrón
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->first();

        // Verificar si ya votó
        if (Voto::where('idPadronElectoral', $padron->idPadronElectoral)->exists()) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('error', 'Ya has votado en esta elección.');
        }

        // Validar datos
        $request->validate([
            'candidatos' => 'required|array',
            'candidatos.*' => 'required|exists:Candidato,idCandidato'
        ]);

        try {
            DB::beginTransaction();

            // Registrar votos
            $votosRegistrados = 0;
            foreach ($request->candidatos as $cargoId => $candidatoId) {
                Voto::create([
                    'idCandidato' => $candidatoId,
                    'idPadronElectoral' => $padron->idPadronElectoral,
                    'fechaVoto' => now(),
                ]);
                $votosRegistrados++;
            }

            \Log::info("Total de votos registrados: {$votosRegistrados}");
            \Log::info('=== VOTACIÓN COMPLETADA EXITOSAMENTE ===');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Tu voto ha sido registrado exitosamente!',
                'votos_registrados' => $votosRegistrados
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al registrar voto: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => 'Hubo un error al registrar tu voto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pantalla de éxito después de votar
     */
    public function votoExitoso($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);
        
        // Obtener el padrón y votos del usuario
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->first();

        // Si no hay padrón (modo demo), usar colección vacía
        $votos = collect([]);
        if ($padron) {
            $votos = Voto::with(['candidato.usuario.perfil', 'candidato.partido', 'candidato.cargo'])
                ->where('idPadronElectoral', $padron->idPadronElectoral)
                ->get();
        }

        return view('votante.votar.exito', compact('eleccion', 'votos'));
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
