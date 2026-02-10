<?php

namespace App\Http\Controllers;

use App\Models\PropuestaCandidato;
use Illuminate\Http\Request;
use App\Models\Elecciones;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PropuestaCandidatoController extends Controller
{
     public function index()
    {
        $elecciones = Elecciones::with([
            'candidatoElecciones.candidato.usuario.perfil',
            'candidatoElecciones.candidato.propuestas',
            'candidatoElecciones.cargo',
            'candidatoElecciones.partido'
        ])->get();
        
        return view('crud.propuesta_candidato.ver', compact('elecciones'));
    }

    public function create()
    {
        $elecciones = \App\Models\Elecciones::with(['partidos.candidatos'])->get();
        return view('crud.propuesta_candidato.crear', compact('elecciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['propuesta'=>'required|string','descripcion'=>'required|string','idCandidato'=>'required|integer']);
        $m = new PropuestaCandidato($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Propuesta creada','data'=>['id'=>$m->getKey(),'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $m = PropuestaCandidato::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Propuesta obtenida','data'=>['propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]]);
    }

    public function edit($id)
    {
        $m = PropuestaCandidato::findOrFail($id);
        $elecciones = \App\Models\Elecciones::with(['partidos.candidatos'])->get();
        return view('crud.propuesta_candidato.editar', compact('m', 'elecciones'));
    }

    public function update(Request $request, $id)
    {
        $m = PropuestaCandidato::findOrFail($id);
        $data = $request->validate(['propuesta'=>'required|string','descripcion'=>'required|string']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Propuesta actualizada','data'=>['id'=>$m->getKey(),'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]]);
    }

    public function destroy($id)
    {
        $m = PropuestaCandidato::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Propuesta eliminada','data'=>['id'=>(int)$id,'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]]);
    }

    public function getCandidatosByEleccion($eleccionId)
    {
        $eleccion = \App\Models\Elecciones::with(['partidos.candidatos.usuario.perfil', 'partidos.candidatos.cargo'])->findOrFail($eleccionId);
        $candidatos = $eleccion->partidos->flatMap->candidatos->map(function($candidato) {
            return [
                'idCandidato' => $candidato->idCandidato,
                'nombre' => $candidato->usuario->perfil
                    ? trim($candidato->usuario->perfil->nombre . ' ' . $candidato->usuario->perfil->apellidoPaterno . ' ' . $candidato->usuario->perfil->apellidoMaterno)
                    : $candidato->usuario->correo,
                'partido' => $candidato->partido->partido,
                'cargo' => $candidato->cargo->cargo
            ];
        });
        return response()->json($candidatos);
    }
}
