<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use App\Interfaces\Services\IArchivoService;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    public function __construct(
        protected IPartidoService $partidoService,
        protected IArchivoService $archivoService
    ) {}

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
            'urlPartido' => 'required|url',
            'descripcion' => 'required|string',
            'planTrabajo' => 'nullable|url',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'partido.required' => 'El nombre del partido es obligatorio.',
            'partido.string' => 'El nombre del partido debe ser texto.',
            'partido.max' => 'El nombre del partido no puede exceder los 255 caracteres.',
            'partido.unique' => 'El nombre del partido ya existe.',
            'urlPartido.required' => 'La URL del partido es obligatoria.',
            'urlPartido.url' => 'La URL del partido debe ser válida.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'planTrabajo.url' => 'El plan de trabajo debe ser una URL válida.',
            'foto.image' => 'La foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser un archivo de tipo jpeg, png, jpg o gif.',
            'foto.max' => 'La foto no puede exceder los 5MB.',
        ]);

        $partido = $this->partidoService->crearPartido($request->all());

        if ($request->hasFile('foto')) {
            $this->partidoService->cambiarFotoPartido($partido, $request->file('foto'));
        }

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

    public function update(Request $request, int $id, IEleccionesService $eleccionesService)
    {
        $partido = $this->partidoService->obtenerPartidoPorId($id);

        if ($request->partido != $partido->partido) {
            $request->validate([
                'partido' => 'nullable|string|max:255|unique:Partido,partido',
            ], [
                'partido.string' => 'El nombre del partido debe ser texto.',
                'partido.max' => 'El nombre del partido no puede exceder los 255 caracteres.',
                'partido.unique' => 'El nombre del partido ya existe.',
            ]);
        }

        $request->validate([
            'urlPartido' => 'nullable|url|max:255',
            'descripcion' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'elecciones' => 'nullable|array',
            'elecciones.*' => 'nullable|exists:Elecciones,idElecciones',
        ], [
            'urlPartido.url' => 'La URL del partido debe ser válida.',
            'urlPartido.max' => 'La URL del partido no puede exceder los 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'elecciones.array' => 'Las elecciones deben ser un array.',
            'elecciones.*.exists' => 'Las elecciones deben existir.'
        ]);

        $this->partidoService->editarPartido([
            'partido' => $request->partido,
            'urlPartido' => $request->urlPartido,
            'descripcion' => $request->descripcion,
            'planTrabajo' => $request->planTrabajo,
        ], $partido);

        if ($request->has('elecciones')) {
            $elecciones = collect($request->elecciones)->map(function ($eleccion) use ($eleccionesService) {
                return $eleccionesService->obtenerEleccionPorId($eleccion);
            });

            $this->partidoService->establecerEleccionesDePartido($partido, $elecciones);
        }

        if ($request->hasFile('foto')) {
            $this->partidoService->cambiarFotoPartido($partido, $request->file('foto'));
        }

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
