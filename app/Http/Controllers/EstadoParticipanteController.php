<?php

namespace App\Http\Controllers;

use App\Models\EstadoParticipante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EstadoParticipanteController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index(){ if ($r = $this->ensureAuthAndPerm('gestion.estado_participante.*')) { return $r; } return view('crud.estado_participante.ver'); }
    public function create(){ if ($r = $this->ensureAuthAndPerm('gestion.estado_participante.*')) { return $r; } return view('crud.estado_participante.crear'); }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_participante.*')) { return $r; }
        $data = $request->validate(['idEstadoParticipante'=>'required|integer','estadoParticipante'=>'required|string|max:100']);
        $m = new EstadoParticipante($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Estado participante creado','data'=>['id'=>$m->getKey(),'estadoParticipante'=>$m->estadoParticipante]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_participante.*')) { return $r; }
        $m = EstadoParticipante::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Estado participante obtenido','data'=>['estadoParticipante'=>$m->estadoParticipante]]);
    }

    public function edit($id){ if ($r = $this->ensureAuthAndPerm('gestion.estado_participante.*')) { return $r; } return view('crud.estado_participante.editar'); }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_participante.*')) { return $r; }
        $m = EstadoParticipante::findOrFail($id);
        $data = $request->validate(['estadoParticipante'=>'required|string|max:100']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Estado participante actualizado','data'=>['id'=>$m->getKey(),'estadoParticipante'=>$m->estadoParticipante]]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.estado_participante.*')) { return $r; }
        $m = EstadoParticipante::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Estado participante eliminado','data'=>['id'=>(int)$id,'estadoParticipante'=>$m->estadoParticipante]]);
    }
}
