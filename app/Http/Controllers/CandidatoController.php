<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CandidatoController extends Controller
{
    public function index()
    {
        $candidatos = Candidato::with(['partido', 'cargo', 'usuario.perfil'])->get();
        return view('crud.candidato.ver', compact('candidatos'));
    }

    public function create()
    {
        $partidos = \App\Models\Partido::all();
        $cargos = \App\Models\Cargo::all();
        $usuarios = \App\Models\User::all();
        return view('crud.candidato.crear', compact('partidos', 'cargos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idPartido' => 'required|integer',
            'idCargo' => 'required|integer',
            'idUsuario' => 'required|integer',
        ]);
        $c = new Candidato($data);
        $c->save();
        return response()->json([
            'success' => true,
            'message' => 'Candidato creado',
            'data' => [
                'id' => $c->getKey(),
                'idPartido' => $c->idPartido,
                'idCargo' => $c->idCargo,
                'idUsuario' => $c->idUsuario,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $c = Candidato::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Candidato obtenido',
            'data' => [
                'idPartido' => $c->idPartido,
                'idCargo' => $c->idCargo,
                'idUsuario' => $c->idUsuario,
            ],
        ]);
    }

    public function edit($id)
    {
        $candidato = Candidato::findOrFail($id);
        $partidos = \App\Models\Partido::all();
        $cargos = \App\Models\Cargo::all();
        $usuarios = \App\Models\User::all();
        return view('crud.candidato.editar', compact('candidato', 'partidos', 'cargos', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        $c = Candidato::findOrFail($id);
        $data = $request->validate([
            'idPartido' => 'required|integer',
            'idCargo' => 'required|integer',
            'idUsuario' => 'required|integer',
        ]);
        $c->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Candidato actualizado',
            'data' => [
                'id' => $c->getKey(),
                'idPartido' => $c->idPartido,
                'idCargo' => $c->idCargo,
                'idUsuario' => $c->idUsuario,
            ],
        ]);
    }

    public function destroy($id)
    {
        $c = Candidato::findOrFail($id);
        $c->delete();
        return response()->json([
            'success' => true,
            'message' => 'Candidato eliminado',
            'data' => [
                'id' => (int) $id,
                'idPartido' => $c->idPartido,
                'idCargo' => $c->idCargo,
                'idUsuario' => $c->idUsuario,
            ],
        ]);
    }
}
