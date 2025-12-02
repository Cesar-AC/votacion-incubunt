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
            'idElecciones' => 'required|integer',
            'partido' => 'required|string|max:255',
            'urlPartido' => 'required|string',
            'descripcion' => 'required|string',
        ]);
        $p = new Partido($data);
        $p->save();
        return response()->json([
            'success' => true,
            'message' => 'Partido creado',
            'data' => [
                'id' => $p->getKey(),
                'idElecciones' => $p->idElecciones,
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
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
                'idElecciones' => $p->idElecciones,
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
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
            'idElecciones' => 'required|integer',
            'partido' => 'required|string|max:255',
            'urlPartido' => 'required|string',
            'descripcion' => 'required|string',
        ]);
        $p->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Partido actualizado',
            'data' => [
                'id' => $p->getKey(),
                'idElecciones' => $p->idElecciones,
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
            ],
        ]);
    }

    public function destroy($id)
    {
        $p = Partido::findOrFail($id);
        $p->delete();
        return response()->json([
            'success' => true,
            'message' => 'Partido eliminado',
            'data' => [
                'id' => (int) $id,
                'idElecciones' => $p->idElecciones,
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
            ],
        ]);
    }
}
