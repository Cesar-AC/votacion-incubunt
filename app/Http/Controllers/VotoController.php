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
        $votos = Voto::with('candidato.usuario.perfil')->get();
        return view('crud.voto.ver', compact('votos'));
    }

    public function create()
    {
        return view('crud.voto.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idCandidato' => 'required|integer',
        ]);
        $v = new Voto($data);
        $v->save();
        return response()->json([
            'success' => true,
            'message' => 'Voto creado',
            'data' => [
                'id' => $v->getKey(),
                'idCandidato' => $v->idCandidato,
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
                'idCandidato' => $v->idCandidato
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
            ],
        ]);
    }
}
