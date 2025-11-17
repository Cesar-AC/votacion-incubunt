<?php

namespace App\Http\Controllers;

use App\Models\PropuestaCandidato;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PropuestaCandidatoController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index(){ if ($r = $this->ensureAuthAndPerm('gestion.propuesta_candidato.*')) { return $r; } return view('crud.propuesta_candidato.ver'); }
    public function create(){ if ($r = $this->ensureAuthAndPerm('gestion.propuesta_candidato.*')) { return $r; } return view('crud.propuesta_candidato.crear'); }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_candidato.*')) { return $r; }
        $data = $request->validate(['propuesta'=>'required|string','descripcion'=>'required|string','idCandidato'=>'required|integer']);
        $m = new PropuestaCandidato($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Propuesta creada','data'=>['id'=>$m->getKey(),'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_candidato.*')) { return $r; }
        $m = PropuestaCandidato::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Propuesta obtenida','data'=>['propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]]);
    }

    public function edit($id){ if ($r = $this->ensureAuthAndPerm('gestion.propuesta_candidato.*')) { return $r; } return view('crud.propuesta_candidato.editar'); }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_candidato.*')) { return $r; }
        $m = PropuestaCandidato::findOrFail($id);
        $data = $request->validate(['propuesta'=>'required|string','descripcion'=>'required|string']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Propuesta actualizada','data'=>['id'=>$m->getKey(),'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_candidato.*')) { return $r; }
        $m = PropuestaCandidato::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Propuesta eliminada','data'=>['id'=>(int)$id,'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idCandidato'=>$m->idCandidato]]);
    }
}
