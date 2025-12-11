<?php

namespace App\Http\Controllers;

use App\Models\ListaVotante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ListaVotanteController extends Controller
{
    public function index(){ return view('crud.lista_votante.ver'); }
    public function create(){ return view('crud.lista_votante.crear'); }

    public function store(Request $request)
    {
        $data = $request->validate(['idUser'=>'required|integer','idElecciones'=>'required|integer','fechaVoto'=>'required|date','idTipoVoto'=>'required|integer']);
        $m = new ListaVotante($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Registro creado','data'=>['id'=>$m->getKey(),'idUser'=>$m->idUser,'idElecciones'=>$m->idElecciones,'fechaVoto'=>$m->fechaVoto,'idTipoVoto'=>$m->idTipoVoto]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $m = ListaVotante::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Registro obtenido','data'=>['idUser'=>$m->idUser,'idElecciones'=>$m->idElecciones,'fechaVoto'=>$m->fechaVoto,'idTipoVoto'=>$m->idTipoVoto]]);
    }

    public function edit($id){ return view('crud.lista_votante.editar'); }

    public function update(Request $request, $id)
    {
        $m = ListaVotante::findOrFail($id);
        $data = $request->validate(['fechaVoto'=>'required|date','idTipoVoto'=>'required|integer']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Registro actualizado','data'=>['id'=>$m->getKey(),'idUser'=>$m->idUser,'idElecciones'=>$m->idElecciones,'fechaVoto'=>$m->fechaVoto,'idTipoVoto'=>$m->idTipoVoto]]);
    }

    public function destroy($id)
    {
        $m = ListaVotante::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Registro eliminado','data'=>['id'=>(int)$id,'idUser'=>$m->idUser,'idElecciones'=>$m->idElecciones,'fechaVoto'=>$m->fechaVoto,'idTipoVoto'=>$m->idTipoVoto]]);
    }
}
