<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PermisoController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index(){ if ($r = $this->ensureAuthAndPerm('gestion.permiso.*')) { return $r; } return view('crud.permiso.ver'); }
    public function create(){ if ($r = $this->ensureAuthAndPerm('gestion.permiso.*')) { return $r; } return view('crud.permiso.crear'); }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.permiso.*')) { return $r; }
        $data = $request->validate(['permiso'=>'required|string']);
        $m = new Permiso($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Permiso creado','data'=>['id'=>$m->getKey(),'permiso'=>$m->permiso]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.permiso.*')) { return $r; }
        $m = Permiso::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Permiso obtenido','data'=>['permiso'=>$m->permiso]]);
    }

    public function edit($id){ if ($r = $this->ensureAuthAndPerm('gestion.permiso.*')) { return $r; } return view('crud.permiso.editar'); }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.permiso.*')) { return $r; }
        $m = Permiso::findOrFail($id);
        $data = $request->validate(['permiso'=>'required|string']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Permiso actualizado','data'=>['id'=>$m->getKey(),'permiso'=>$m->permiso]]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.permiso.*')) { return $r; }
        $m = Permiso::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Permiso eliminado','data'=>['id'=>(int)$id,'permiso'=>$m->permiso]]);
    }
}
