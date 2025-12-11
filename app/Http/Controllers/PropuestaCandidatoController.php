<?php

namespace App\Http\Controllers;

use App\Models\PropuestaCandidato;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PropuestaCandidatoController extends Controller
{
    public function index(){ return view('crud.propuesta_candidato.ver'); }
    public function create(){ return view('crud.propuesta_candidato.crear'); }

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

    public function edit($id){ return view('crud.propuesta_candidato.editar'); }

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
}
