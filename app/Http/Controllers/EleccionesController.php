<?php

namespace App\Http\Controllers;

use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use Illuminate\Http\Request;
use App\Interfaces\Services\IEleccionesService;

class EleccionesController extends Controller
{
    private IEleccionesService $eleccionesService;

    public function __construct(IEleccionesService $eleccionesService)
    {
        $this->eleccionesService = $eleccionesService;
    }

    public function index()
    {
        $elecciones = Elecciones::with(['estadoEleccion'])
            ->withCount('usuarios') // cuenta padrón electoral
            ->orderBy('fechaInicio', 'desc')
            ->get();

        $eleccionActiva = null;
        try {
            $eleccionActiva = $this->eleccionesService->obtenerEleccionActiva();
        } catch (\Exception $e) {
            // sin elección activa configurada, omitimos
        }

        return view('crud.elecciones.ver', compact('elecciones', 'eleccionActiva'));
    }

    public function show(int $id)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

        return response()->json([
            'success' => true,
            'message' => 'Elección obtenida',
            'data' => [
                'titulo' => $eleccion->titulo,
                'descripcion' => $eleccion->descripcion,
                'fechaInicio' => $eleccion->fechaInicio,
                'fechaCierre' => $eleccion->fechaCierre,
                'estado' => $eleccion->estadoEleccion(),
                'partidos' => $eleccion->partidos()->pluck('idPartido'),
            ],
        ]);
    }

    public function create()
    {
        $estados = EstadoElecciones::all();
        return view('crud.elecciones.crear', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'fechaInicio' => 'required|date|after:now',
                'fechaCierre' => 'required|date|after:fechaInicio',
            ],
            [
                'titulo.required' => 'El título es obligatorio.',
                'titulo.string' => 'El título debe ser una cadena de texto.',
                'titulo.max' => 'El título no puede exceder los 255 caracteres.',
                'descripcion.required' => 'La descripción es obligatoria.',
                'descripcion.string' => 'La descripción debe ser una cadena de texto.',
                'fechaInicio.required' => 'La fecha de inicio es obligatoria.',
                'fechaInicio.date' => 'La fecha de inicio debe ser una fecha válida.',
                'fechaInicio.after' => 'La fecha de inicio debe ser posterior a la fecha de hoy.',
                'fechaCierre.required' => 'La fecha de cierre es obligatoria.',
                'fechaCierre.date' => 'La fecha de cierre debe ser una fecha válida.',
                'fechaCierre.after' => 'La fecha de cierre debe ser posterior a la fecha de inicio.',
            ]
        );

        $this->eleccionesService->crearElecciones($request->all());

        return redirect()->route('crud.elecciones.ver')->with('success', 'Elección creada correctamente');
    }

    public function edit($id)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

        $estados = EstadoElecciones::all();
        return view('crud.elecciones.editar', compact('eleccion', 'estados'));
    }

    public function update(Request $request, int $id)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

        $request->validate(
            [
                'titulo' => 'string|max:255',
                'descripcion' => 'string',
                'fechaInicio' => 'date|after:now',
                'fechaCierre' => 'date|after:fechaInicio',
                'idEstado' => 'integer|exists:EstadoElecciones,idEstado',
            ],
            [
                'titulo.string' => 'El título debe ser una cadena de texto.',
                'titulo.max' => 'El título no puede exceder los 255 caracteres.',
                'descripcion.string' => 'La descripción debe ser una cadena de texto.',
                'fechaInicio.date' => 'La fecha de inicio debe ser una fecha válida.',
                'fechaInicio.after' => 'La fecha de inicio debe ser posterior a la fecha de hoy.',
                'fechaCierre.date' => 'La fecha de cierre debe ser una fecha válida.',
                'fechaCierre.after' => 'La fecha de cierre debe ser posterior a la fecha de inicio.',
                'idEstado.integer' => 'El estado debe ser un número entero.',
                'idEstado.exists' => 'El estado no existe.',
            ]
        );

        $this->eleccionesService->editarElecciones($request->all(), $eleccion);

        return redirect()->route('crud.elecciones.ver')->with('success', 'Elección actualizada correctamente');
    }

    public function destroy(Request $request, int $id)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

        $this->eleccionesService->anularElecciones($eleccion);
        return response()->json([
            'success' => true,
            'message' => 'Elección anulada',
            'data' => [
                'id' => $eleccion->getKey(),
                'titulo' => $eleccion->titulo,
                'descripcion' => $eleccion->descripcion,
                'fechaInicio' => $eleccion->fechaInicio,
                'fechaCierre' => $eleccion->fechaCierre,
                'estado' => $eleccion->estadoEleccion(),
            ],
        ]);
    }

    public function finalizar(Request $request, int $id)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

        $this->eleccionesService->finalizarElecciones($eleccion);

        return response()->json([
            'success' => true,
            'message' => 'Elección finalizada',
            'data' => [
                'id' => $eleccion->getKey(),
                'titulo' => $eleccion->titulo,
                'descripcion' => $eleccion->descripcion,
                'fechaInicio' => $eleccion->fechaInicio,
                'fechaCierre' => $eleccion->fechaCierre,
                'estado' => $eleccion->estadoEleccion(),
            ],
        ]);
    }

    public function restaurar(Request $request, int $id)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

        $this->eleccionesService->restaurarElecciones($eleccion);

        return response()->json([
            'success' => true,
            'message' => 'Elección restaurada',
            'data' => [
                'id' => $eleccion->getKey(),
                'titulo' => $eleccion->titulo,
                'descripcion' => $eleccion->descripcion,
                'fechaInicio' => $eleccion->fechaInicio,
                'fechaCierre' => $eleccion->fechaCierre,
                'estado' => $eleccion->estadoEleccion(),
            ],
        ]);
    }
}
