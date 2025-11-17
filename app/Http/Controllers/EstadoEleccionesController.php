<?php

namespace App\Http\Controllers;

use App\Models\EstadoElecciones;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EstadoEleccionesController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index() { if ($r = $this->ensureAuthAndPerm('gestion.estado_elecciones.*')) { return $r; } return view('crud.estado_elecciones.ver'); }
    public function create() { if ($r = $this->ensureAuthAndPerm('gestion.estado_elecciones.*')) { return $r; } return view('crud.estado_elecciones.crear'); }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_elecciones.*')) { return $r; }
        $data = $request->validate(['estado' => 'required|string|max:100']);
        $m = new EstadoElecciones($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Estado creado','data'=>['id'=>$m->getKey(),'estado'=>$m->estado]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_elecciones.*')) { return $r; }
        $m = EstadoElecciones::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Estado obtenido','data'=>['estado'=>$m->estado]]);
    }

    public function edit($id) { if ($r = $this->ensureAuthAndPerm('gestion.estado_elecciones.*')) { return $r; } return view('crud.estado_elecciones.editar'); }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_elecciones.*')) { return $r; }
        $m = EstadoElecciones::findOrFail($id);
        $data = $request->validate(['estado' => 'required|string|max:100']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Estado actualizado','data'=>['id'=>$m->getKey(),'estado'=>$m->estado]]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_elecciones.*')) { return $r; }
        $m = EstadoElecciones::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Estado eliminado','data'=>['id'=>(int)$id,'estado'=>$m->estado]]);
    }
}
