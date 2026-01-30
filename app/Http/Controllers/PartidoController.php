<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    public function __construct(protected IPartidoService $partidoService) {}

    public function index()
    {
        $partidos = $this->partidoService->obtenerPartidos();

        return view('crud.partido.ver', compact('partidos'));
    }

    public function create()
    {
        return view('crud.partido.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'partido' => 'required|string|max:255|unique:Partido,partido',
            'urlPartido' => 'required|url|max:255',
            'descripcion' => 'required|string',
            'tipo' => 'nullable|string|max:255',
        ], [
            'partido.required' => 'El nombre del partido es obligatorio.',
            'partido.string' => 'El nombre del partido debe ser texto.',
            'partido.max' => 'El nombre del partido no puede exceder los 255 caracteres.',
            'partido.unique' => 'El nombre del partido ya existe.',
            'urlPartido.required' => 'La URL del partido es obligatoria.',
            'urlPartido.url' => 'La URL del partido debe ser válida.',
            'urlPartido.max' => 'La URL del partido no puede exceder los 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'tipo.string' => 'El tipo debe ser texto.',
            'tipo.max' => 'El tipo no puede exceder los 255 caracteres.',
        ]);

        $this->partidoService->crearPartido($request->all());

        return redirect()
            ->route('crud.partido.ver')
            ->with('success', 'Partido creado correctamente');
    }


    public function show(int $id)
    {
        $p = $this->partidoService->obtenerPartidoPorId($id);
        return response()->json([
            'success' => true,
            'message' => 'Partido obtenido',
            'data' => [
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
                'tipo' => $p->tipo,
                'planTrabajo' => $p->planTrabajo,
                'elecciones' => $p->elecciones()->pluck('idElecciones'),
            ],
        ]);
    }

    public function edit(int $id, IEleccionesService $eleccionesService)
    {
        $partido = $this->partidoService->obtenerPartidoPorId($id);
        $elecciones = $eleccionesService->obtenerTodasLasEleccionesProgramables();
        return view('crud.partido.editar', compact('partido', 'elecciones'));
    }

    public function update(Request $request, int $id)
    {
        $partido = $this->partidoService->obtenerPartidoPorId($id);

        $request->validate([
            'partido' => 'nullable|string|max:255|unique:Partido,partido',
            'urlPartido' => 'nullable|url|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'nullable|string|max:255',
        ], [
            'partido.string' => 'El nombre del partido debe ser texto.',
            'partido.max' => 'El nombre del partido no puede exceder los 255 caracteres.',
            'partido.unique' => 'El nombre del partido ya existe.',
            'urlPartido.url' => 'La URL del partido debe ser válida.',
            'urlPartido.max' => 'La URL del partido no puede exceder los 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'tipo.string' => 'El tipo debe ser texto.',
            'tipo.max' => 'El tipo no puede exceder los 255 caracteres.',
        ]);

        $this->partidoService->editarPartido($request->all(), $partido);

        return redirect()
            ->route('crud.partido.ver')
            ->with('success', 'Partido actualizado correctamente');
    }

    public function destroy($id)
    {
        $partido = $this->partidoService->obtenerPartidoPorId($id);

        $this->partidoService->eliminarPartido($partido);

        return response()->json([
            'success' => true,
            'message' => 'Partido eliminado',
            'data' => [
                'id' => (int) $id,
                'partido' => $partido->partido,
                'urlPartido' => $partido->urlPartido,
                'descripcion' => $partido->descripcion,
                'tipo' => $partido->tipo,
            ],
        ]);
    }
}
