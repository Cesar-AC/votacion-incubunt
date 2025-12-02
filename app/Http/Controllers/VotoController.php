<?php

namespace App\Http\Controllers;

use App\Models\Voto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class VotoController extends Controller
{
    public function index()
    {
        return view('crud.voto.ver');
    }

    public function create()
    {
        return view('crud.voto.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idCandidato' => 'required|integer',
            'idElecciones' => 'required|integer',
            'fechaVoto' => 'required|date',
        ]);
        $v = new Voto($data);
        $v->save();
        return response()->json([
            'success' => true,
            'message' => 'Voto creado',
            'data' => [
                'id' => $v->getKey(),
                'idCandidato' => $v->idCandidato,
                'idElecciones' => $v->idElecciones,
                'fechaVoto' => $v->fechaVoto,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $v = Voto::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Voto obtenido',
            'data' => [
                'idCandidato' => $v->idCandidato,
                'idElecciones' => $v->idElecciones,
                'fechaVoto' => $v->fechaVoto,
            ],
        ]);
    }

    public function edit($id)
    {
        return view('crud.voto.editar');
    }

    public function update(Request $request, $id)
    {
        $v = Voto::findOrFail($id);
        $data = $request->validate([
            'fechaVoto' => 'required|date',
        ]);
        $v->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Voto actualizado',
            'data' => [
                'id' => $v->getKey(),
                'idCandidato' => $v->idCandidato,
                'idElecciones' => $v->idElecciones,
                'fechaVoto' => $v->fechaVoto,
            ],
        ]);
    }

    public function destroy($id)
    {
        $v = Voto::findOrFail($id);
        $v->delete();
        return response()->json([
            'success' => true,
            'message' => 'Voto eliminado',
            'data' => [
                'id' => (int) $id,
                'idCandidato' => $v->idCandidato,
                'idElecciones' => $v->idElecciones,
                'fechaVoto' => $v->fechaVoto,
            ],
        ]);
    }
}
