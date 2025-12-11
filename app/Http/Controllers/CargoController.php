<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CargoController extends Controller
{
    public function index()
    {
        return view('crud.cargo.ver');
    }

    public function create()
    {
        return view('crud.cargo.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idCargo' => 'required|integer',
            'cargo' => 'required|string|max:100',
            'idArea' => 'required|integer',
        ]);
        $c = new Cargo($data);
        $c->save();
        return response()->json([
            'success' => true,
            'message' => 'Cargo creado',
            'data' => [
                'id' => $c->getKey(),
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $c = Cargo::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Cargo obtenido',
            'data' => [
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ]);
    }

    public function edit($id)
    {
        return view('crud.cargo.editar');
    }

    public function update(Request $request, $id)
    {
        $c = Cargo::findOrFail($id);
        $data = $request->validate([
            'cargo' => 'required|string|max:100',
            'idArea' => 'required|integer',
        ]);
        $c->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Cargo actualizado',
            'data' => [
                'id' => $c->getKey(),
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ]);
    }

    public function destroy($id)
    {
        $c = Cargo::findOrFail($id);
        $c->delete();
        return response()->json([
            'success' => true,
            'message' => 'Cargo eliminado',
            'data' => [
                'id' => (int) $id,
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ]);
    }
}
