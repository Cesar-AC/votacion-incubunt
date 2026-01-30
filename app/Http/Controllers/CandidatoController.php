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
            'candidatos.cargo.area',
            'candidatos.partido',
            'partidos.candidatos.usuario.perfil',
            'partidos.candidatos.cargo.area',
            'partidos.candidatos.partido'
        ])->get();

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
        $request->validate([
            'idUsuario' => 'required|integer|exists:User,idUser|unique:Candidato,idUsuario',
        ], [
            'idUsuario.required' => 'El usuario es obligatorio.',
            'idUsuario.integer' => 'El ID del usuario debe ser un número.',
            'idUsuario.exists' => 'El usuario no es válido.',
            'idUsuario.unique' => 'El usuario ya tiene un candidato vinculado.',
        ]);

        $candidato = $this->candidatoService->crearCandidato([
            'idUsuario' => $request->idUsuario,
        ]);

        if (isset($request->idElecciones) && isset($request->idCargo)) {
            $request->validate([
                'idElecciones' => 'required|integer|exists:Elecciones,idElecciones',
                'idCargo' => 'required|integer|exists:Cargo,idCargo',
                'idPartido' => 'nullable|integer|exists:Partido,idPartido',
            ], [
                'idElecciones.required' => 'La elección es obligatoria.',
                'idElecciones.integer' => 'El ID de la elección debe ser un número.',
                'idElecciones.exists' => 'La elección no es válida.',
                'idCargo.required' => 'El cargo es obligatorio.',
                'idCargo.integer' => 'El ID del cargo debe ser un número.',
                'idCargo.exists' => 'El cargo no es válido.',
                'idPartido.integer' => 'El ID del partido debe ser un número.',
                'idPartido.exists' => 'El partido no es válido.',
            ]);

            $eleccion = $this->eleccionesService->obtenerEleccionPorId($request->idElecciones);

            $this->candidatoService->vincularCandidatoAEleccion([
                'idCargo' => $request->idCargo,
                'idPartido' => $request->idPartido,
            ], $candidato, $eleccion);
        }

        return redirect()
            ->route('crud.candidato.ver')
            ->with('success', 'Candidatos creados correctamente.');
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
