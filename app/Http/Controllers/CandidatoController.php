<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\ICandidatoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Partido;
use App\Models\PartidoEleccion;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;
use App\Interfaces\Services\IEleccionesService;

class CandidatoController extends Controller
{
    public function __construct(
        protected ICandidatoService $candidatoService,
        protected IEleccionesService $eleccionesService,
    ) {}

    public function index()
    {
        $elecciones = \App\Models\Elecciones::with([
            'candidatos.usuario.perfil',
            'candidatos.candidatoElecciones.cargo.area',
            'candidatos.candidatoElecciones.partido',
        ])->get();

        // Procesar candidatos para cada elección y agregar cargo/partido desde pivot
        foreach ($elecciones as $eleccion) {
            foreach ($eleccion->candidatos as $candidato) {
                // Buscar la relación específica para esta elección
                $candidatoEleccion = $candidato->candidatoElecciones
                    ->where('idElecciones', $eleccion->idElecciones)
                    ->first();
                
                if ($candidatoEleccion) {
                    // Agregar cargo y partido como propiedades directas para facilitar acceso en la vista
                    $candidato->cargo = $candidatoEleccion->cargo;
                    $candidato->partido = $candidatoEleccion->partido;
                    $candidato->idPartido = $candidatoEleccion->idPartido;
                    $candidato->idCargo = $candidatoEleccion->idCargo;
                } else {
                    // Asegurar que las propiedades existan aunque sean null
                    $candidato->cargo = null;
                    $candidato->partido = null;
                    $candidato->idPartido = null;
                    $candidato->idCargo = null;
                }
            }
        }

        return view('crud.candidato.ver', compact('elecciones'));
    }

    public function create()
    {
        $partidos   = \App\Models\Partido::all();
        $cargos     = \App\Models\Cargo::with('area')->get();
        $usuarios   = \App\Models\User::with('perfil')->get();
        $elecciones = \App\Models\Elecciones::all();

        return view('crud.candidato.crear', compact(
            'partidos',
            'cargos',
            'usuarios',
            'elecciones'
        ));
    }

    public function store(Request $request)
    {
        // Validar estructura de datos
        $request->validate([
            'idEleccion' => 'required|integer|exists:Elecciones,idElecciones',
            'candidatos' => 'required|array|min:1',
            'candidatos.*.idUsuario' => 'required|integer|exists:User,idUser',
            'candidatos.*.idCargo' => 'required|integer|exists:Cargo,idCargo',
            'candidatos.*.idPartido' => 'nullable|integer|exists:Partido,idPartido',
            'candidatos.*.planTrabajo' => 'nullable|string|max:1000',
        ], [
            'idEleccion.required' => 'Debe seleccionar una elección.',
            'idEleccion.exists' => 'La elección seleccionada no es válida.',
            'candidatos.required' => 'Debe agregar al menos un candidato.',
            'candidatos.min' => 'Debe agregar al menos un candidato.',
            'candidatos.*.idUsuario.required' => 'Cada candidato debe tener un usuario asignado.',
            'candidatos.*.idUsuario.exists' => 'Uno de los usuarios seleccionados no es válido.',
            'candidatos.*.idCargo.required' => 'Cada candidato debe tener un cargo asignado.',
            'candidatos.*.idCargo.exists' => 'Uno de los cargos seleccionados no es válido.',
            'candidatos.*.idPartido.exists' => 'Uno de los partidos seleccionados no es válido.',
        ]);

        $candidatosCreados = [];
        $candidatosFallidos = [];
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($request->idEleccion);

        DB::beginTransaction();

        try {
            foreach ($request->candidatos as $index => $candidatoData) {
                try {
                    // Verificar si el usuario ya es candidato
                    $candidatoExistente = \App\Models\Candidato::where('idUsuario', $candidatoData['idUsuario'])->first();
                    
                    if ($candidatoExistente) {
                        // Si ya es candidato, solo vincular a la elección
                        $candidato = $candidatoExistente;
                        $accion = 'vinculado a la elección';
                    } else {
                        // Crear nuevo candidato
                        $candidato = $this->candidatoService->crearCandidato([
                            'idUsuario' => $candidatoData['idUsuario'],
                        ]);
                        $accion = 'creado y vinculado';
                    }

                    // Vincular a la elección
                    $this->candidatoService->vincularCandidatoAEleccion([
                        'idCargo' => $candidatoData['idCargo'],
                        'idPartido' => $candidatoData['idPartido'] ?? null,
                    ], $candidato, $eleccion);

                    $usuario = \App\Models\User::with('perfil')->find($candidatoData['idUsuario']);
                    $nombreUsuario = $usuario->perfil->nombre ?? $usuario->correo;
                    
                    $candidatosCreados[] = $nombreUsuario . ' (' . $accion . ')';

                } catch (\Exception $e) {
                    $usuario = \App\Models\User::with('perfil')->find($candidatoData['idUsuario']);
                    $nombreUsuario = $usuario->perfil->nombre ?? $usuario->correo ?? 'Usuario ID ' . $candidatoData['idUsuario'];
                    
                    $candidatosFallidos[] = [
                        'nombre' => $nombreUsuario,
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Error al crear candidato', [
                        'index' => $index,
                        'data' => $candidatoData,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Si todos fallaron, hacer rollback
            if (empty($candidatosCreados)) {
                DB::rollBack();
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'error_general' => 'No se pudo crear ningún candidato.',
                        'detalles' => $candidatosFallidos
                    ]);
            }

            DB::commit();

            // Preparar mensaje de éxito
            $mensaje = count($candidatosCreados) . ' candidato(s) procesado(s) correctamente.';
            
            // Si hubo algunos que fallaron, agregar advertencia
            if (!empty($candidatosFallidos)) {
                return redirect()
                    ->route('crud.candidato.ver')
                    ->with('success', $mensaje)
                    ->with('warning', count($candidatosFallidos) . ' candidato(s) no pudo(pudieron) ser procesado(s).')
                    ->with('candidatos_exitosos', $candidatosCreados)
                    ->with('candidatos_fallidos', $candidatosFallidos);
            }

            return redirect()
                ->route('crud.candidato.ver')
                ->with('success', $mensaje)
                ->with('candidatos_exitosos', $candidatosCreados);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error general al crear candidatos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error_general' => 'Error al procesar los candidatos: ' . $e->getMessage()]);
        }
    }


    public function show($id)
    {
        $candidato = $this->candidatoService->obtenerCandidatoPorId($id);

        return response()->json([
            'success' => true,
            'message' => 'Candidato obtenido',
            'data' => [
                'idPartido' => $candidato->idPartido,
                'idCargo' => $candidato->idCargo,
                'idUsuario' => $candidato->idUsuario,
            ],
        ]);
    }

    public function edit($id)
    {
        $candidato = $this->candidatoService->obtenerCandidatoPorId($id);

        $partidos = \App\Models\Partido::all();
        $cargos = \App\Models\Cargo::all();
        $usuarios = \App\Models\User::all();
        $elecciones = \App\Models\Elecciones::all();
        return view('crud.candidato.editar', compact('candidato', 'partidos', 'cargos', 'usuarios', 'elecciones'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'idUsuario' => 'nullable|integer|exists:User,idUser',
            'idElecciones' => 'nullable|integer|exists:Elecciones,idElecciones',
            'idCargo' => 'nullable|integer|exists:Cargo,idCargo',
            'idPartido' => 'nullable|integer|exists:Partido,idPartido',
        ]);

        $candidato = $this->candidatoService->obtenerCandidatoPorId($id);

        if (isset($request->idUsuario)) {
            $this->candidatoService->editarCandidato([
                'idUsuario' => $request->idUsuario,
            ], $candidato);
        }

        if (isset($request->idElecciones)) {
            if (isset($request->idCargo)) {
                $eleccion = $this->eleccionesService->obtenerEleccionPorId($request->idElecciones);
                $this->candidatoService->actualizarDatosDeCandidatoEnElecciones([
                    'idCargo' => $request->idCargo,
                ], $candidato, $eleccion);
            }

            if (isset($request->idPartido)) {
                $eleccion = $this->eleccionesService->obtenerEleccionPorId($request->idElecciones);
                $this->candidatoService->actualizarDatosDeCandidatoEnElecciones([
                    'idPartido' => $request->idPartido,
                ], $candidato, $eleccion);
            }
        }

        return redirect()->route('crud.candidato.ver')
            ->with('success', 'Candidato actualizado correctamente.');
    }

    public function destroy($id)
    {
        $candidato = $this->candidatoService->obtenerCandidatoPorId($id);
        $this->candidatoService->eliminarCandidato($candidato);

        return redirect()->route('crud.candidato.ver')
            ->with('success', 'Candidato eliminado correctamente.');
    }
}
