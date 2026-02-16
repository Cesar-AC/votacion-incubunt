<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use Illuminate\Http\Request;

class PropuestaPartidoController extends Controller
{
    public function __construct(
        protected IEleccionesService $eleccionesService,
        protected IPartidoService $partidoService
    ) {}

    public function index()
    {
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();
        return view('crud.propuesta_partido.ver', compact('elecciones'));
    }

    public function create()
    {
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();
        return view('crud.propuesta_partido.crear', compact('elecciones'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'idEleccion' => 'required|integer|exists:Elecciones,idElecciones',
            'idPartido' => 'required|integer|exists:Partido,idPartido',
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'required|string'
        ], [
            'idEleccion.required' => 'La elección es obligatoria.',
            'idEleccion.integer' => 'La elección debe ser un número entero.',
            'idEleccion.exists' => 'La elección no existe.',
            'idPartido.required' => 'El partido es obligatorio.',
            'idPartido.integer' => 'El partido debe ser un número entero.',
            'idPartido.exists' => 'El partido no existe.',
            'propuesta.required' => 'La propuesta es obligatoria.',
            'propuesta.string' => 'La propuesta debe ser una cadena de texto.',
            'propuesta.max' => 'La propuesta debe tener máximo 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
        ]);

        $eleccion = $this->eleccionesService->obtenerEleccionPorId($request->idEleccion);
        $partido = $this->partidoService->obtenerPartidoPorId($request->idPartido);

        try {
            $this->partidoService->añadirPropuestaDePartido([
                'propuesta' => $datos['propuesta'],
                'descripcion' => $datos['descripcion'],
            ], $partido, $eleccion);
        } catch (\Exception $e) {
            return back()->withErrors('No se creó la propuesta: ' . $e->getMessage());
        }

        return redirect()->route('crud.propuesta_partido.ver')->with('success', 'La propuesta se creó correctamente.');
    }

    public function show($id)
    {
        return redirect()->route('crud.propuesta_partido.ver');
    }

    public function edit(Request $request, int $idPropuesta)
    {
        $propuesta = $this->partidoService->obtenerPropuestaDePartido($idPropuesta);
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();

        return view('crud.propuesta_partido.editar', compact('propuesta', 'elecciones'));
    }

    public function update(Request $request, int $idPropuesta)
    {
        $propuesta = $this->partidoService->obtenerPropuestaDePartido($idPropuesta);

        $datos = $request->validate([
            'idEleccion' => 'required|integer|exists:Elecciones,idElecciones',
            'idPartido' => 'required|integer|exists:Partido,idPartido',
            'propuesta' => 'required|string|max:255',
            'descripcion' => 'string'
        ], [
            'idEleccion.required' => 'La elección es obligatoria.',
            'idEleccion.integer' => 'La elección debe ser un número entero.',
            'idEleccion.exists' => 'La elección no existe.',
            'idPartido.required' => 'El partido es obligatorio.',
            'idPartido.integer' => 'El partido debe ser un número entero.',
            'idPartido.exists' => 'El partido no existe.',
            'propuesta.required' => 'La propuesta es obligatoria.',
            'propuesta.string' => 'La propuesta debe ser una cadena de texto.',
            'propuesta.max' => 'La propuesta debe tener máximo 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
        ]);

        $this->partidoService->actualizarPropuestaDePartido([
            'idEleccion' => $datos['idEleccion'],
            'idPartido' => $datos['idPartido'],
            'propuesta' => $datos['propuesta'],
            'descripcion' => $datos['descripcion'],
        ], $propuesta);

        return redirect()->route('crud.propuesta_partido.ver')->with('success', 'La propuesta se actualizó correctamente.');
    }

    public function destroy(int $idPropuesta)
    {
        $propuesta = $this->partidoService->obtenerPropuestaDePartido($idPropuesta);
        $this->partidoService->eliminarPropuestaDePartido($propuesta);

        return redirect()->route('crud.propuesta_partido.ver')->with('success', 'La propuesta se eliminó correctamente.');
    }

    public function getPartidosByEleccion($eleccionId)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($eleccionId);
        $partidos = $this->partidoService->obtenerPartidosInscritosEnEleccion($eleccion)->map(function ($partido) {
            return [
                'idPartido' => $partido->idPartido,
                'partido' => $partido->partido
            ];
        });

        return response()->json($partidos);
    }
}
