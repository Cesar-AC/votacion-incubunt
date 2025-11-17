<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index(){ if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; } return view('crud.rol.ver'); }
    public function create(){ if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; } return view('crud.rol.crear'); }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; }
        $data = $request->validate(['rol'=>'required|string']);
        $m = new Rol($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Rol creado','data'=>['id'=>$m->getKey(),'rol'=>$m->rol]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; }
        $m = Rol::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Rol obtenido','data'=>['rol'=>$m->rol]]);
    }

    public function edit($id){ if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; } return view('crud.rol.editar'); }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; }
        $m = Rol::findOrFail($id);
        $data = $request->validate(['rol'=>'required|string']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Rol actualizado','data'=>['id'=>$m->getKey(),'rol'=>$m->rol]]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; }
        $m = Rol::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Rol eliminado','data'=>['id'=>(int)$id,'rol'=>$m->rol]]);
    }

    public function agregarPermiso(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.rol.*')) { return $r; }
        $m = Rol::findOrFail($id);
        $data = $request->validate(['idPermiso'=>'required|integer']);
        $permiso = Permiso::findOrFail($data['idPermiso']);
        $m->permisos()->syncWithoutDetaching([$permiso->getKey()]);
        return response()->json(['success'=>true,'message'=>'Permiso agregado','data'=>['id'=>$m->getKey(),'rol'=>$m->rol,'permiso_id'=>$permiso->getKey()]]);
    }
}
