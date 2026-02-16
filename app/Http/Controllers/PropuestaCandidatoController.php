<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\ICandidatoService;
use App\Interfaces\Services\IEleccionesService;
use App\Models\Candidato;
use App\Models\PropuestaCandidato;
use Illuminate\Http\Request;
use App\Models\Elecciones;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PropuestaCandidatoController extends Controller
{
    public function __construct(
        protected IEleccionesService $eleccionesService,
        protected ICandidatoService $candidatoService,
    ) {}

    public function index()
    {
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();

        return view('crud.propuesta_candidato.ver', compact('elecciones'));
    }

    public function create()
    {
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();

        return view('crud.propuesta_candidato.crear', compact('elecciones'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'idEleccion' => 'required|integer|exists:Elecciones,idElecciones',
            'idCandidato' => 'required|integer|exists:Candidato,idCandidato',
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'required|string'
        ], [
            'idEleccion.required' => 'La elección es obligatoria.',
            'idEleccion.integer' => 'La elección debe ser un número entero.',
            'idEleccion.exists' => 'La elección no existe.',
            'idCandidato.required' => 'El candidato es obligatorio.',
            'idCandidato.integer' => 'El candidato debe ser un número entero.',
            'idCandidato.exists' => 'El candidato no existe.',
            'propuesta.required' => 'La propuesta es obligatoria.',
            'propuesta.string' => 'La propuesta debe ser una cadena de texto.',
            'propuesta.max' => 'La propuesta debe tener máximo 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
        ]);

        $eleccion = $this->eleccionesService->obtenerEleccionPorId($datos['idEleccion']);
        $candidato = $this->candidatoService->obtenerCandidatoPorId($datos['idCandidato']);

        $this->candidatoService->añadirPropuestaDeCandidato([
            'propuesta' => $datos['propuesta'],
            'descripcion' => $datos['descripcion'],
        ], $candidato, $eleccion);

        return redirect()->route('crud.propuesta_candidato.ver')->with('success', 'La propuesta se creó correctamente.');
    }

    public function show(int $idPropuesta)
    {
        return redirect()->route('crud.propuesta_candidato.ver');
    }

    public function edit(int $idPropuesta)
    {
        $propuesta = $this->candidatoService->obtenerPropuestaDeCandidato($idPropuesta);
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();
        $candidatos = $this->candidatoService->obtenerCandidatosInscritosEnEleccion($propuesta->elecciones);

        $eleccionesService = $this->eleccionesService;

        return view('crud.propuesta_candidato.editar', compact('propuesta', 'elecciones', 'candidatos', 'eleccionesService'));
    }

    public function update(Request $request, int $idPropuesta)
    {
        $propuesta = $this->candidatoService->obtenerPropuestaDeCandidato($idPropuesta);

        $datos = $request->validate([
            'idEleccion' => 'required|integer|exists:Elecciones,idElecciones',
            'idCandidato' => 'required|integer|exists:Candidato,idCandidato',
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'string'
        ], [
            'idEleccion.required' => 'La elección es obligatoria.',
            'idEleccion.integer' => 'La elección debe ser un número entero.',
            'idEleccion.exists' => 'La elección no existe.',
            'idCandidato.required' => 'El candidato es obligatorio.',
            'idCandidato.integer' => 'El candidato debe ser un número entero.',
            'idCandidato.exists' => 'El candidato no existe.',
            'propuesta.required' => 'La propuesta es obligatoria.',
            'propuesta.string' => 'La propuesta debe ser una cadena de texto.',
            'propuesta.max' => 'La propuesta debe tener máximo 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
        ]);

        $eleccion = $this->eleccionesService->obtenerEleccionPorId($datos['idEleccion']);
        $candidato = $this->candidatoService->obtenerCandidatoPorId($datos['idCandidato']);

        $this->candidatoService->actualizarPropuestaDeCandidato([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'propuesta' => $datos['propuesta'],
            'descripcion' => $datos['descripcion'],
        ], $propuesta);

        return redirect()->route('crud.propuesta_candidato.ver')->with('success', 'La propuesta se actualizó correctamente.');
    }

    public function destroy(int $idPropuesta)
    {
        $propuesta = $this->candidatoService->obtenerPropuestaDeCandidato($idPropuesta);
        $this->candidatoService->eliminarPropuestaDeCandidato($propuesta);

        return redirect()->route('crud.propuesta_candidato.ver')->with('success', 'La propuesta se eliminó correctamente.');
    }

    public function getCandidatosByEleccion(int $idEleccion)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($idEleccion);

        $candidatos = $this->candidatoService->obtenerCandidatosInscritosEnEleccion($eleccion);

        $candidatos = $candidatos->map(function (Candidato $candidato) use ($eleccion) {
            $candidatoEleccion = $this->eleccionesService->obtenerCandidatoEleccion($candidato, $eleccion);

            return [
                'idCandidato' => $candidato->idCandidato,
                'nombre' => $candidato->usuario->perfil
                    ? $candidato->usuario->perfil->obtenerNombreApellido()
                    : $candidato->usuario->correo,
                'partido' => $candidatoEleccion->partido?->partido ?? 'Sin partido',
                'cargo' => $candidatoEleccion->cargo->cargo
            ];
        });
        return response()->json($candidatos);
    }
}
