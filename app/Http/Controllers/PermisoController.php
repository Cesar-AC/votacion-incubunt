<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PermisoController extends Controller
{
    public function index(){ return view('crud.permiso.ver'); }
    public function create(){ return view('crud.permiso.crear'); }

    public function store(Request $request)
    {
        $data = $request->validate(['permiso'=>'required|string']);
        $m = new Permiso($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Permiso creado','data'=>['id'=>$m->getKey(),'permiso'=>$m->permiso]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $m = Permiso::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Permiso obtenido','data'=>['permiso'=>$m->permiso]]);
    }

    public function edit($id){ 
        $permiso = Permiso::findOrFail($id);
        return view('crud.permiso.editar', compact('permiso')); 
    }

    public function update(Request $request, $id)
    {
        $m = Permiso::findOrFail($id);
        $data = $request->validate(['permiso'=>'required|string']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Permiso actualizado','data'=>['id'=>$m->getKey(),'permiso'=>$m->permiso]]);
    }

    public function destroy($id)
    {
        $m = Permiso::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Permiso eliminado','data'=>['id'=>(int)$id,'permiso'=>$m->permiso]]);
    }
}
