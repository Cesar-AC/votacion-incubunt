<?php

namespace App\Http\Controllers;

use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EleccionesController extends Controller
{
    public function index()
    {
        return view('crud.elecciones.ver');
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
        return view('crud.elecciones.crear');
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

    private static function validDates(Carbon $inicio, Carbon $fin): ?string
    {
        if ($inicio->isPast()) {
            return back()->withErrors([
                'fechaInicio' => 'La fecha de inicio de la elección no puede ser anterior al día de hoy.'
            ])->withInput();
        }

        if ($fin->isPast()) {
            return back()->withErrors([
                'fechaCierre' => 'La fecha de cierre de la elección no puede ser anterior al día de hoy.'
            ])->withInput();
        }

        if ($fin->lessThan($inicio)) {
            return back()->withErrors([
                'fechaCierre' => 'La fecha de cierre de la elección no puede ser anterior a la fecha de inicio.'
            ])->withInput();
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
        if ($errorFechas) return $errorFechas;

        /*
        if ($this->overlapsExisting(null, $inicio, $fin)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una elección en el rango de fechas indicado',
            ], Response::HTTP_CONFLICT);
        }
        */

        $e = Elecciones::create([
    'titulo' => $data['titulo'],
    'descripcion' => $data['descripcion'],
    'fechaInicio' => $fechaInicio,
    'fechaCierre' => $fechaCierre,
    'idEstado' => EstadoElecciones::PROGRAMADO,
]);
        $e->save();

        return response()->json([
            'success' => true,
            'message' => 'Elección creada correctamente',
            'data' => [
                'id' => $e->getKey(),
                'titulo' => $e->titulo,
                'descripcion' => $e->descripcion,
                'fechaInicio' => $e->fechaInicio,
                'fechaCierre' => $e->fechaCierre,
                'estado' => $e->estadoEleccion->nombre ?? null,
            ],
        ], Response::HTTP_CREATED);
    }

    public function edit($id)
    {
        return view('crud.elecciones.editar');
    }

    public function update(Request $request, Elecciones $id)
    {
        $e = $id;
        $data = $request->validate([
            'titulo' => 'string|max:255',
            'descripcion' => 'string',
            'fechaInicio' => 'date',
            'fechaCierre' => 'date',
            'idEstado' => 'integer',
        ]);

        if ($e->estaFinalizado()) {
            return back()->withErrors([
                'idEleccion' => 'No se pueden modificar elecciones que están finalizadas.'
            ])->withInput();
        }

        if (empty($data)) {
            return back()->withErrors([
                'idEleccion' => 'No se han proporcionado datos para actualizar.'
            ])->withInput();
        }

        isset($data['fechaInicio']) 
            ? $fechaInicio = Carbon::parse($data['fechaInicio']) 
            : $fechaInicio = Carbon::parse($e->fechaInicio);

        isset($data['fechaCierre'])
            ? $fechaCierre = Carbon::parse($data['fechaCierre']) 
            : $fechaCierre = Carbon::parse($e->fechaCierre);

        $errorFechas = self::validDates($fechaInicio, $fechaCierre);
        if ($errorFechas) return $errorFechas;

        if (isset($data['titulo'])) $e->titulo = $data['titulo'];
        if (isset($data['descripcion'])) $e->descripcion = $data['descripcion'];
        if (isset($data['fechaInicio'])) $e->fechaInicio = $fechaInicio;
        if (isset($data['fechaCierre'])) $e->fechaCierre = $fechaCierre;
        if (isset($data['idEstado'])) $e->idEstado = $data['idEstado'];

        $e->save();
        return response()->json([
            'success' => true,
            'message' => 'Elección actualizada correctamente',
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
