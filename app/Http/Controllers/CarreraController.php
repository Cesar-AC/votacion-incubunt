<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::all();
        return view('crud.carrera.ver', compact('carreras'));
    }

    public function create()
    {
        return view('crud.carrera.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idCarrera' => 'required|integer',
            'carrera' => 'required|string|max:100',
        ]);

        $carrera = new Carrera($data);
        $carrera->save();

        return response()->json([
            'success' => true,
            'message' => 'Carrera creada correctamente',
            'data' => [
                'id' => $carrera->getKey(),
                'carrera' => $carrera->carrera,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $carrera = Carrera::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Carrera obtenida',
            'data' => [
                'carrera' => $carrera->carrera,
            ],
        ]);
    }

    public function edit($id)
    {
        $carrera = Carrera::findOrFail($id);
        return view('crud.carrera.editar', compact('carrera'));
    }

    public function update(Request $request, $id)
    {
        $carrera = Carrera::findOrFail($id);

        $data = $request->validate([
            'carrera' => 'required|string|max:100',
        ]);

        $carrera->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Carrera actualizada correctamente',
            'data' => [
                'id' => $carrera->getKey(),
                'carrera' => $carrera->carrera,
            ],
        ]);
    }

    public function destroy($id)
    {
        $carrera = Carrera::findOrFail($id);
        $carrera->delete();

        return response()->json([
            'success' => true,
            'message' => 'Carrera eliminada correctamente',
            'data' => [
                'id' => (int) $id,
                'carrera' => $carrera->carrera,
            ],
        ]);
    }
}
