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

    public function show($id)
    {
        $e = Elecciones::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Elección obtenida',
            'data' => [
                'titulo' => $e->titulo,
                'fecha_inicio' => $e->fecha_inicio,
                'fecha_cierre' => $e->fecha_cierre,
                'descripcion' => $e->descripcion,
                'estado' => $e->estado,
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

    public function store(Request $request)
    {
        // Normalizar fechas a string para que pase la validación aunque el test envíe Carbon
        $fi = $request->input('fecha_inicio');
        $fc = $request->input('fecha_cierre');
        if ($fi instanceof Carbon) { $fi = $fi->toDateTimeString(); }
        if ($fc instanceof Carbon) { $fc = $fc->toDateTimeString(); }
        if (!is_null($fi) || !is_null($fc)) {
            $request->merge([
                'fecha_inicio' => $fi,
                'fecha_cierre' => $fc,
            ]);
        }
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $inicio = Carbon::parse($data['fecha_inicio']);
        $fin = Carbon::parse($data['fecha_cierre']);

        if ($this->overlapsExisting(null, $inicio, $fin)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una elección en el rango de fechas indicado',
            ], Response::HTTP_CONFLICT);
        }

        // Asegurar estado por defecto existente
        $estadoId = EstadoElecciones::query()->value('idEstado');
        if (!$estadoId) {
            $nuevoEstado = new EstadoElecciones(['estado' => 'Programada']);
            $nuevoEstado->save();
            $estadoId = $nuevoEstado->getKey();
        }

        $e = new Elecciones([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'fecha_inicio' => $inicio,
            'fecha_cierre' => $fin,
            'estado' => $estadoId,
        ]);
        $e->save();

        return response()->json([
            'success' => true,
            'message' => 'Elección creada correctamente',
            'data' => [
                'id' => $e->getKey(),
                'titulo' => $e->titulo,
                'fecha_inicio' => $e->fecha_inicio,
                'fecha_cierre' => $e->fecha_cierre,
                'descripcion' => $e->descripcion,
                'estado' => $e->estado,
            ],
        ], Response::HTTP_CREATED);
    }

    public function edit($id)
    {
        return view('crud.elecciones.editar');
    }

    public function update(Request $request, $id)
    {
        $e = Elecciones::findOrFail($id);
        // Normalizar fechas a string para que pase la validación aunque el test envíe Carbon
        $fi = $request->input('fecha_inicio');
        $fc = $request->input('fecha_cierre');
        if ($fi instanceof Carbon) { $fi = $fi->toDateTimeString(); }
        if ($fc instanceof Carbon) { $fc = $fc->toDateTimeString(); }
        if (!is_null($fi) || !is_null($fc)) {
            $request->merge([
                'fecha_inicio' => $fi,
                'fecha_cierre' => $fc,
            ]);
        }
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|integer',
        ]);

        $inicio = Carbon::parse($data['fecha_inicio']);
        $fin = Carbon::parse($data['fecha_cierre']);
        if ($this->overlapsExisting($e->getKey(), $inicio, $fin)) {
            return response()->json([
                'success' => false,
                'message' => 'Las fechas se sobreponen con otra elección',
            ], Response::HTTP_CONFLICT);
        }

        $e->titulo = $data['titulo'];
        $e->descripcion = $data['descripcion'];
        $e->fecha_inicio = $inicio;
        $e->fecha_cierre = $fin;
        if (array_key_exists('estado', $data)) {
            $e->estado = $data['estado'];
        }
        $e->save();

        return response()->json([
            'success' => true,
            'message' => 'Elección actualizada correctamente',
            'data' => [
                'id' => $e->getKey(),
                'titulo' => $e->titulo,
                'fecha_inicio' => $e->fecha_inicio,
                'fecha_cierre' => $e->fecha_cierre,
                'descripcion' => $e->descripcion,
                'estado' => $e->estado,
            ],
        ]);
    }

    public function destroy($id)
    {
        $e = Elecciones::findOrFail($id);
        $e->delete();
        return response()->json([
            'success' => true,
            'message' => 'Elección eliminada',
            'data' => [
                'id' => (int) $id,
                'titulo' => $e->titulo,
                'fecha_inicio' => $e->fecha_inicio,
                'fecha_cierre' => $e->fecha_cierre,
                'descripcion' => $e->descripcion,
                'estado' => $e->estado,
            ],
        ]);
    }
}
