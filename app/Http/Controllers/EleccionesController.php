<?php

namespace App\Http\Controllers;

use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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

    public function show(Request $request, Elecciones $e)
    {
        return response()->json([
            'success' => true,
            'message' => 'Elección obtenida',
            'data' => [
                'titulo' => $e->titulo,
                'descripcion' => $e->descripcion,
                'fechaInicio' => $e->fechaInicio,
                'fechaCierre' => $e->fechaCierre,
                'estado' => $e->estadoEleccion(),
                'partidos' => $e->partidos()->pluck('idPartido'),
            ],
        ]);
    }

    public function create()
    {
        $estados = EstadoElecciones::all();
        return view('crud.elecciones.crear', compact('estados'));
    }

    private function overlapsExisting(?int $excludeId, Carbon $inicio, Carbon $fin): bool
    {
        $q = Elecciones::query();
        if ($excludeId) {
            $q->where('idElecciones', '!=', $excludeId);
        }
        $rows = $q->get(['fecha_inicio','fecha_cierre']);
        $ns = $inicio->getTimestamp();
        $ne = $fin->getTimestamp();
        foreach ($rows as $row) {
            $es = Carbon::parse($row->fecha_inicio)->getTimestamp();
            $ee = Carbon::parse($row->fecha_cierre)->getTimestamp();
            if ($es <= $ne && $ee >= $ns) {
                return true;
            }
        }
        return false;
    }

    private static function validDates(Carbon $inicio, Carbon $fin, bool $permitirPasadas = false): ?string
{
    if (! $permitirPasadas && $inicio->isPast()) {
        return 'La fecha de inicio no puede ser anterior a hoy.';
    }

    if (! $permitirPasadas && $fin->isPast()) {
        return 'La fecha de cierre no puede ser anterior a hoy.';
    }

    if ($fin->lessThan($inicio)) {
        return 'La fecha de cierre no puede ser anterior a la fecha de inicio.';
    }
    return null;
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fechaInicio' => 'required|date',
            'fechaCierre' => 'required|date',
        ]);

        $fechaInicio = Carbon::parse($data['fechaInicio']);
        $fechaCierre = Carbon::parse($data['fechaCierre']);

        $errorFechas = self::validDates($fechaInicio, $fechaCierre);
        if ($errorFechas) {
            return back()->withErrors(['fechas' => $errorFechas])->withInput();
        }

        try {
            $e = Elecciones::create([
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion'],
                'fechaInicio' => $fechaInicio,
                'fechaCierre' => $fechaCierre,
                'idEstado' => EstadoElecciones::PROGRAMADO,
            ]);

            return redirect()
                ->route('crud.elecciones.ver')
                ->with('success', 'Elección creada correctamente');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear la elección: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $eleccion = Elecciones::findOrFail($id);
        $estados = EstadoElecciones::all();
        return view('crud.elecciones.editar', compact('eleccion', 'estados'));
    }

    public function update(Request $request, Elecciones $id)
    {
        $e = $id;
        
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fechaInicio' => 'required|date',
            'fechaCierre' => 'required|date',
            'idEstado' => 'required|integer',
        ]);

        try {
            if ($e->estaFinalizado()) {
                return back()->withErrors([
                    'error' => 'No se pueden modificar elecciones que están finalizadas.'
                ])->withInput();
            }

            $fechaInicio = Carbon::parse($data['fechaInicio']);
            $fechaCierre = Carbon::parse($data['fechaCierre']);

            $errorFechas = self::validDates($fechaInicio, $fechaCierre, true);
            if ($errorFechas) {
                return back()->withErrors([
                    'fechas' => $errorFechas
                ])->withInput();
            }

            $e->titulo = $data['titulo'];
            $e->descripcion = $data['descripcion'];
            $e->fechaInicio = $fechaInicio;
            $e->fechaCierre = $fechaCierre;
            $e->idEstado = $data['idEstado'];
            $e->save();

            return redirect()
                ->route('crud.elecciones.ver')
                ->with('success', 'Elección actualizada correctamente');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar la elección: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function destroy(Request $request, Elecciones $id)
    {
        $e = $id;
        $e->delete();
        return response()->json([
            'success' => true,
            'message' => 'Elección eliminada',
            'data' => [
                'id' => $e->getKey(),
                'titulo' => $e->titulo,
                'descripcion' => $e->descripcion,
                'fechaInicio' => $e->fechaInicio,
                'fechaCierre' => $e->fechaCierre,
                'estado' => $e->estadoEleccion(),
            ],
        ]);
    }
}
