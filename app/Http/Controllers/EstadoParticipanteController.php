<?php

namespace App\Http\Controllers;

use App\Models\EstadoParticipante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EstadoParticipanteController extends Controller
{
    public function index(){ return view('crud.estado_participante.ver'); }
    public function create(){ return view('crud.estado_participante.crear'); }

    public function store(Request $request)
    {
        $data = $request->validate(['idEstadoParticipante'=>'required|integer','estadoParticipante'=>'required|string|max:100']);
        $m = new EstadoParticipante($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Estado participante creado','data'=>['id'=>$m->getKey(),'estadoParticipante'=>$m->estadoParticipante]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $m = EstadoParticipante::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Estado participante obtenido','data'=>['estadoParticipante'=>$m->estadoParticipante]]);
    }

    public function edit($id){ return view('crud.estado_participante.editar'); }

    public function update(Request $request, $id)
    {
        $m = EstadoParticipante::findOrFail($id);
        $data = $request->validate(['estadoParticipante'=>'required|string|max:100']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Estado participante actualizado','data'=>['id'=>$m->getKey(),'estadoParticipante'=>$m->estadoParticipante]]);
    }

    public function destroy($id)
    {
        $m = EstadoParticipante::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Estado participante eliminado','data'=>['id'=>(int)$id,'estadoParticipante'=>$m->estadoParticipante]]);
    }
}
