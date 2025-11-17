<?php

namespace App\Http\Controllers;

use App\Models\PadronElectoral;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PadronElectoralController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) { return view('auth.login'); }
        $user = Auth::user();
        if (!$user->permisos()->where('permiso', $permiso)->exists()) { abort(404); }
        return null;
    }

    public function index()
    {
        if ($r = $this->ensureAuthAndPerm('gestion.padron_electoral.*')) { return $r; }
        return view('crud.padron_electoral.ver');
    }

    public function create()
    {
        if ($r = $this->ensureAuthAndPerm('gestion.padron_electoral.*')) { return $r; }
        return view('crud.padron_electoral.crear');
    }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.padron_electoral.*')) { return $r; }
        $data = $request->validate([
            'idElecciones' => 'required|integer',
            'idUser' => 'required|integer',
            'idEstadoParticipante' => 'required|integer',
        ]);
        $p = new PadronElectoral($data);
        $p->save();
        return response()->json([
            'success' => true,
            'message' => 'Padrón creado',
            'data' => [
                'id' => $p->getKey(),
                'idElecciones' => $p->idElecciones,
                'idUser' => $p->idUser,
                'idEstadoParticipante' => $p->idEstadoParticipante,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.padron_electoral.*')) { return $r; }
        $p = PadronElectoral::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Padrón obtenido',
            'data' => [
                'idElecciones' => $p->idElecciones,
                'idUser' => $p->idUser,
                'idEstadoParticipante' => $p->idEstadoParticipante,
            ],
        ]);
    }

    public function edit($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.padron_electoral.*')) { return $r; }
        return view('crud.padron_electoral.editar');
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.padron_electoral.*')) { return $r; }
        $p = PadronElectoral::findOrFail($id);
        $data = $request->validate([
            'idEstadoParticipante' => 'required|integer',
        ]);
        $p->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Padrón actualizado',
            'data' => [
                'id' => $p->getKey(),
                'idElecciones' => $p->idElecciones,
                'idUser' => $p->idUser,
                'idEstadoParticipante' => $p->idEstadoParticipante,
            ],
        ]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.padron_electoral.*')) { return $r; }
        $p = PadronElectoral::findOrFail($id);
        $p->delete();
        return response()->json([
            'success' => true,
            'message' => 'Padrón eliminado',
            'data' => [
                'id' => (int) $id,
                'idElecciones' => $p->idElecciones,
                'idUser' => $p->idUser,
                'idEstadoParticipante' => $p->idEstadoParticipante,
            ],
        ]);
    }
}
