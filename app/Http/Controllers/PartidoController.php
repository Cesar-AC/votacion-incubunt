<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PartidoController extends Controller
{
    public function index()
    {
        return view('crud.partido.ver');
    }

    public function create()
    {
        return view('crud.partido.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partido' => 'required|string|max:255',
            'urlPartido' => 'required|string',
            'descripcion' => 'required|string',
            'elecciones' => 'required|array',
            'elecciones.*' => 'integer',
        ]);
        $p = new Partido([
            'partido' => $data['partido'],
            'urlPartido' => $data['urlPartido'],
            'descripcion' => $data['descripcion'],
        ]);
        $p->save();
        $p->elecciones()->sync($data['elecciones']);
        return response()->json([
            'success' => true,
            'message' => 'Partido creado',
            'data' => [
                'id' => $p->getKey(),
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
                'elecciones' => $p->elecciones()->pluck('idElecciones'),
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $p = Partido::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Partido obtenido',
            'data' => [
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
                'elecciones' => $p->elecciones()->pluck('idElecciones'),
            ],
        ]);
    }

    public function edit($id)
    {
        return view('crud.partido.editar');
    }

    public function update(Request $request, $id)
    {
        $p = Partido::findOrFail($id);
        $data = $request->validate([
            'partido' => 'required|string|max:255',
            'urlPartido' => 'required|string',
            'descripcion' => 'required|string',
            'elecciones' => 'required|array',
            'elecciones.*' => 'integer',
        ]);
        $p->update([
            'partido' => $data['partido'],
            'urlPartido' => $data['urlPartido'],
            'descripcion' => $data['descripcion'],
        ]);
        $p->elecciones()->sync($data['elecciones']);
        return response()->json([
            'success' => true,
            'message' => 'Partido actualizado',
            'data' => [
                'id' => $p->getKey(),
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
                'elecciones' => $p->elecciones()->pluck('idElecciones'),
            ],
        ]);
    }

    public function destroy($id)
    {
        $p = Partido::findOrFail($id);
        $p->elecciones()->detach();
        $p->delete();
        return response()->json([
            'success' => true,
            'message' => 'Partido eliminado',
            'data' => [
                'id' => (int) $id,
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
            ],
        ]);
    }
}
