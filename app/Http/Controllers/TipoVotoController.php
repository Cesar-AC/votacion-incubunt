<?php

namespace App\Http\Controllers;

use App\Models\TipoVoto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TipoVotoController extends Controller
{
    public function index()
    {
        return view('crud.tipo_voto.ver');
    }

    public function create()
    {
        return view('crud.tipo_voto.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idTipoVoto' => 'required|integer',
            'tipoVoto' => 'required|string|max:255',
        ]);
        $t = new TipoVoto($data);
        $t->save();
        return response()->json([
            'success' => true,
            'message' => 'Tipo de voto creado',
            'data' => [
                'id' => $t->getKey(),
                'tipoVoto' => $t->tipoVoto,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $t = TipoVoto::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Tipo de voto obtenido',
            'data' => [
                'tipoVoto' => $t->tipoVoto,
            ],
        ]);
    }

    public function edit($id)
    {
        return view('crud.tipo_voto.editar');
    }

    public function update(Request $request, $id)
    {
        $t = TipoVoto::findOrFail($id);
        $data = $request->validate([
            'tipoVoto' => 'required|string|max:255',
        ]);
        $t->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Tipo de voto actualizado',
            'data' => [
                'id' => $t->getKey(),
                'tipoVoto' => $t->tipoVoto,
            ],
        ]);
    }

    public function destroy($id)
    {
        $t = TipoVoto::findOrFail($id);
        $t->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tipo de voto eliminado',
            'data' => [
                'id' => (int) $id,
                'tipoVoto' => $t->tipoVoto,
            ],
        ]);
    }
}
