<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\ICandidatoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use App\Interfaces\Services\IUserService;
use App\Models\Area;
use Illuminate\Support\Facades\Validator;

class CandidatoController extends Controller
{
    public function __construct(
        protected ICandidatoService $candidatoService,
        protected IEleccionesService $eleccionesService,
    ) {}

    public function index(IPartidoService $partidoService)
    {
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();
        $eleccionesService = $this->eleccionesService;

        return view('crud.candidato.ver', compact('elecciones', 'eleccionesService', 'partidoService'));
    }

    public function create(IPartidoService $partidoService, IUserService $userService, IAreaService $areaService)
    {
        $partidos   = $partidoService->obtenerPartidos();
        $areas = $areaService->obtenerAreas();
        $usuarios   = $userService->obtenerUsuarios();
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();

        $areaPresidencia = $areaService->obtenerAreaPorId(Area::PRESIDENCIA);
        $cargosPresidencia = $areaPresidencia->cargos;

        $eleccionesService = $this->eleccionesService;
        $cargosPorArea = $areas->pluck('cargos', 'idArea');

        return view('crud.candidato.crear', compact(
            'eleccionesService',
            'partidos',
            'areas',
            'usuarios',
            'elecciones',
            'cargosPorArea',
            'cargosPresidencia'
        ));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'idEleccion' => 'required|integer|exists:Elecciones,idElecciones',
                'candidatos' => 'required|array|min:1',
                'candidatos.*.tipo' => 'required|in:individual,grupal'
            ],
            [
                'idEleccion.required' => 'Debe seleccionar una elección.',
                'idEleccion.exists' => 'La elección seleccionada no es válida.',
                'candidatos.required' => 'Debe agregar al menos un candidato.',
                'candidatos.array' => 'Los candidatos deben ser un array.',
                'candidatos.min' => 'Debe agregar al menos un candidato.',
                'candidatos.*.tipo.required' => 'Alguno de los candidatos no tiene un tipo de candidatura asignado.',
                'candidatos.*.tipo.in' => 'Alguno de los candidatos tiene un tipo de candidatura no válido.',
            ]
        );

        $individuales = array_filter($request->all()['candidatos'], function ($candidato) {
            return $candidato['tipo'] == 'individual';
        });

        $grupales = array_filter($request->all()['candidatos'], function ($candidato) {
            return $candidato['tipo'] == 'grupal';
        });

        if (count($individuales) > 0) {
            $validador = Validator::make($individuales, [
                'candidatos' => 'array',
                'candidatos.*.idUsuario' => 'required|integer|exists:PadronElectoral,idUsuario',
                'candidatos.*.idCargo' => 'required|integer|exists:Cargo,idCargo',
                'candidatos.*.idPartido' => 'nullable|integer|exists:Partido,idPartido',
                'candidatos.*.planTrabajo' => 'nullable|url|max:255',
            ], [
                'candidatos.array' => 'Los candidatos deben ser un array.',
                'candidatos.*.idUsuario.required' => 'Cada candidato debe tener un usuario asignado.',
                'candidatos.*.idUsuario.exists' => 'Cada candidato debe tener un usuario registrado en el padrón electoral.',
                'candidatos.*.idCargo.required' => 'Cada candidato debe tener un cargo asignado.',
                'candidatos.*.idCargo.exists' => 'Cada cargo debe ser un cargo válido.',
                'candidatos.*.idPartido.exists' => 'Cada partido debe ser un partido válido.',
                'candidatos.*.planTrabajo.url' => 'El plan de trabajo debe ser una URL válida.',
                'candidatos.*.planTrabajo.max' => 'El plan de trabajo no puede exceder los 255 caracteres.',
            ]);

            $validador->validate();
        }

        if (count($grupales) > 0) {
            $validador = Validator::make($grupales, [
                'candidatos' => 'array',
                'candidatos.*.idUsuario' => 'required|integer|exists:PadronElectoral,idUsuario',
                'candidatos.*.idCargo' => 'required|integer|exists:Cargo,idCargo',
                'candidatos.*.idPartido' => 'required|integer|exists:Partido,idPartido',
                'candidatos.*.planTrabajo' => 'nullable|url|max:255',
            ], [
                'candidatos.array' => 'Los candidatos deben ser un array.',
                'candidatos.*.idUsuario.required' => 'Cada candidato miembro de partido debe tener un usuario asignado.',
                'candidatos.*.idUsuario.exists' => 'Cada candidato miembro de partido debe tener un usuario registrado en el padrón electoral.',
                'candidatos.*.idCargo.required' => 'Cada candidato miembro de partido debe tener un cargo asignado.',
                'candidatos.*.idCargo.exists' => 'Cada cargo debe ser un cargo válido.',
                'candidatos.*.idPartido.required' => 'Cada candidato miembro de partido debe tener un partido asignado.',
                'candidatos.*.idPartido.exists' => 'Cada partido debe ser un partido válido.',
                'candidatos.*.planTrabajo.url' => 'El plan de trabajo debe ser una URL válida.',
                'candidatos.*.planTrabajo.max' => 'El plan de trabajo no puede exceder los 255 caracteres.',
            ]);

            $validador->validate();
        }

        $eleccion = $this->eleccionesService->obtenerEleccionPorId($request->idEleccion);

        DB::transaction(function () use ($eleccion, $individuales, $grupales) {
            foreach ($individuales as $candidato) {
                $modeloCandidato = $this->candidatoService->crearCandidato([
                    'idUsuario' => $candidato['idUsuario'],
                    'planTrabajo' => $candidato['planTrabajo']
                ]);

                $this->candidatoService->vincularCandidatoAEleccion([
                    'idCargo' => $candidato['idCargo'],
                ], $modeloCandidato, $eleccion);
            }

            foreach ($grupales as $candidato) {
                $modeloCandidato = $this->candidatoService->crearCandidato([
                    'idUsuario' => $candidato['idUsuario'],
                    'planTrabajo' => $candidato['planTrabajo']
                ]);

                $this->candidatoService->vincularCandidatoAEleccion([
                    'idCargo' => $candidato['idCargo'],
                    'idPartido' => $candidato['idPartido'],
                ], $modeloCandidato, $eleccion);
            }
        });

        return redirect()->route('crud.candidato.ver')->with('success', 'Los candidatos se han registrado correctamente.');
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

    public function edit(int $eleccion, int $candidato, IPartidoService $partidoService, IUserService $userService, IAreaService $areaService)
    {
        $candidato = $this->candidatoService->obtenerCandidatoPorId($candidato);
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccion);
        $candidatoEleccion = $this->eleccionesService->obtenerCandidatoEleccion($candidato, $eleccion);

        $partidos   = $partidoService->obtenerPartidos();
        $areas = $areaService->obtenerAreas();
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();

        $areaPresidencia = $areaService->obtenerAreaPorId(Area::PRESIDENCIA);
        $cargosPresidencia = $areaPresidencia->cargos;

        $eleccionesService = $this->eleccionesService;
        $cargosPorArea = $areas->pluck('cargos', 'idArea');

        return view('crud.candidato.editar', compact(
            'eleccionesService',
            'eleccion',
            'candidato',
            'candidatoEleccion',
            'partidos',
            'areas',
            'elecciones',
            'cargosPorArea',
            'cargosPresidencia'
        ));
    }

    public function update(Request $request, int $eleccion, int $candidato)
    {
        $request->validate([
            'tipo' => 'required|in:individual,grupal',
            'planTrabajo' => 'nullable|url|max:255',
        ], [
            'tipo.required' => 'El tipo de candidato es requerido.',
            'tipo.in' => 'El tipo de candidato debe ser individual o grupal.',
            'planTrabajo.url' => 'El plan de trabajo debe ser una URL válida.',
            'planTrabajo.max' => 'El plan de trabajo no puede exceder los 255 caracteres.',
        ]);

        $candidato = $this->candidatoService->obtenerCandidatoPorId($candidato);
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccion);

        DB::transaction(function () use ($request, $candidato, $eleccion) {
            if ($request->tipo == 'individual') {
                $request->validate([
                    'idCargo' => 'required|integer|exists:Cargo,idCargo',
                ], [
                    'idCargo.required' => 'El cargo es requerido.',
                    'idCargo.exists' => 'El cargo no existe.',
                ]);

                $candidatoEleccion = $this->eleccionesService->obtenerCandidatoEleccion($candidato, $eleccion);
                $candidatoEleccion->idPartido = null;
                $candidatoEleccion->idCargo = $request->idCargo;
                $candidato->planTrabajo = $request->planTrabajo;
                $candidato->save();
                $candidatoEleccion->save();
            }

            if ($request->tipo == 'grupal') {
                $request->validate([
                    'idCargo' => 'required|integer|exists:Cargo,idCargo',
                    'idPartido' => 'required|integer|exists:Partido,idPartido',
                ], [
                    'idCargo.required' => 'El cargo es requerido.',
                    'idCargo.exists' => 'El cargo no existe.',
                    'idPartido.required' => 'El partido es requerido.',
                    'idPartido.exists' => 'El partido no existe.',
                ]);

                $candidatoEleccion = $this->eleccionesService->obtenerCandidatoEleccion($candidato, $eleccion);
                $candidatoEleccion->idPartido = $request->idPartido;
                $candidatoEleccion->idCargo = $request->idCargo;
                $candidato->planTrabajo = $request->planTrabajo;
                $candidato->save();
                $candidatoEleccion->save();
            }
        });

        return redirect()->route('crud.candidato.ver')
            ->with('success', 'Candidato actualizado correctamente.');
    }

    public function destroy(int $eleccion, int $candidato)
    {
        $candidato = $this->candidatoService->obtenerCandidatoPorId($candidato);
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccion);
        $candidatoEleccion = $this->eleccionesService->obtenerCandidatoEleccion($candidato, $eleccion);
        $candidatoEleccion->delete();

        return redirect()->route('crud.candidato.ver')
            ->with('success', 'Candidato eliminado correctamente.');
    }
}
