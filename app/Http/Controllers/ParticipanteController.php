<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ParticipanteController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index(){ if ($r = $this->ensureAuthAndPerm('gestion.participante.*')) { return $r; } return view('crud.participante.ver'); }
    public function create(){ if ($r = $this->ensureAuthAndPerm('gestion.participante.*')) { return $r; } return view('crud.participante.crear'); }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.participante.*')) { return $r; }
        $data = $request->validate(['nombre'=>'required|string','apellidos'=>'required|string','idUser'=>'required|integer','idCarrera'=>'required|integer','biografia'=>'required|string','experiencia'=>'required|string','estado'=>'required|integer']);
        $m = new Participante($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Participante creado','data'=>['id'=>$m->getKey(),'nombre'=>$m->nombre,'apellidos'=>$m->apellidos,'idUser'=>$m->idUser,'idCarrera'=>$m->idCarrera,'biografia'=>$m->biografia,'experiencia'=>$m->experiencia,'estado'=>$m->estado]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.participante.*')) { return $r; }
        $m = Participante::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Participante obtenido','data'=>['nombre'=>$m->nombre,'apellidos'=>$m->apellidos,'idUser'=>$m->idUser,'idCarrera'=>$m->idCarrera,'biografia'=>$m->biografia,'experiencia'=>$m->experiencia,'estado'=>$m->estado]]);
    }

    public function edit($id){ if ($r = $this->ensureAuthAndPerm('gestion.participante.*')) { return $r; } return view('crud.participante.editar'); }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.participante.*')) { return $r; }
        $m = Participante::findOrFail($id);
        $data = $request->validate(['nombre'=>'required|string','apellidos'=>'required|string','biografia'=>'required|string','experiencia'=>'required|string','estado'=>'required|integer']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Participante actualizado','data'=>['id'=>$m->getKey(),'nombre'=>$m->nombre,'apellidos'=>$m->apellidos,'idUser'=>$m->idUser,'idCarrera'=>$m->idCarrera,'biografia'=>$m->biografia,'experiencia'=>$m->experiencia,'estado'=>$m->estado]]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.participante.*')) { return $r; }
        $m = Participante::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Participante eliminado','data'=>['id'=>(int)$id,'nombre'=>$m->nombre,'apellidos'=>$m->apellidos,'idUser'=>$m->idUser,'idCarrera'=>$m->idCarrera,'biografia'=>$m->biografia,'experiencia'=>$m->experiencia,'estado'=>$m->estado]]);
    }
}
