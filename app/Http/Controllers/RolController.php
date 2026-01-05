<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    public function index(){ 
        $roles = Rol::all();
        return view('crud.rol.ver', compact('roles')); 
    }
    public function create(){ return view('crud.rol.crear'); }

    public function store(Request $request)
    {
        $data = $request->validate(['rol'=>'required|string']);
        $m = new Rol($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Rol creado','data'=>['id'=>$m->getKey(),'rol'=>$m->rol]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $m = Rol::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Rol obtenido','data'=>['rol'=>$m->rol]]);
    }

    public function edit($id){ 
        $rol = Rol::findOrFail($id);
        return view('crud.rol.editar', compact('rol')); 
    }

    public function update(Request $request, $id)
    {
        $m = Rol::findOrFail($id);
        $data = $request->validate(['rol'=>'required|string']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Rol actualizado','data'=>['id'=>$m->getKey(),'rol'=>$m->rol]]);
    }

    public function destroy($id)
    {
        $m = Rol::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Rol eliminado','data'=>['id'=>(int)$id,'rol'=>$m->rol]]);
    }

    public function agregarPermiso(Request $request, $id)
    {
        $m = Rol::findOrFail($id);
        $data = $request->validate(['idPermiso'=>'required|integer']);
        $permiso = Permiso::findOrFail($data['idPermiso']);
        $m->permisos()->syncWithoutDetaching([$permiso->getKey()]);
        return response()->json(['success'=>true,'message'=>'Permiso agregado','data'=>['id'=>$m->getKey(),'rol'=>$m->rol,'permiso_id'=>$permiso->getKey()]]);
    }
}
