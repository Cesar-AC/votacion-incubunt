<?php

namespace App\Http\Controllers;

use App\Models\PropuestaPartido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PropuestaPartidoController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index(){ if ($r = $this->ensureAuthAndPerm('gestion.propuesta_partido.*')) { return $r; } return view('crud.propuesta_partido.ver'); }
    public function create(){ if ($r = $this->ensureAuthAndPerm('gestion.propuesta_partido.*')) { return $r; } return view('crud.propuesta_partido.crear'); }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_partido.*')) { return $r; }
        $data = $request->validate(['propuesta'=>'required|string','descripcion'=>'required|string','idPartido'=>'required|integer']);
        $m = new PropuestaPartido($data); $m->save();
        return response()->json(['success'=>true,'message'=>'Propuesta creada','data'=>['id'=>$m->getKey(),'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idPartido'=>$m->idPartido]], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_partido.*')) { return $r; }
        $m = PropuestaPartido::findOrFail($id);
        return response()->json(['success'=>true,'message'=>'Propuesta obtenida','data'=>['propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idPartido'=>$m->idPartido]]);
    }

    public function edit($id){ if ($r = $this->ensureAuthAndPerm('gestion.propuesta_partido.*')) { return $r; } return view('crud.propuesta_partido.editar'); }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_partido.*')) { return $r; }
        $m = PropuestaPartido::findOrFail($id);
        $data = $request->validate(['propuesta'=>'required|string','descripcion'=>'required|string']);
        $m->update($data);
        return response()->json(['success'=>true,'message'=>'Propuesta actualizada','data'=>['id'=>$m->getKey(),'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idPartido'=>$m->idPartido]]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.propuesta_partido.*')) { return $r; }
        $m = PropuestaPartido::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true,'message'=>'Propuesta eliminada','data'=>['id'=>(int)$id,'propuesta'=>$m->propuesta,'descripcion'=>$m->descripcion,'idPartido'=>$m->idPartido]]);
    }
}
