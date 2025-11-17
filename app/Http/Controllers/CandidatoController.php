<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CandidatoController extends Controller
{
    private function ensureAuthAndPerm(string $permiso)
    {
        if (!Auth::check()) {
            return view('auth.login');
        }
        $user = Auth::user();
        $hasPerm = $user->permisos()->where('permiso', $permiso)->exists();
        if (!$hasPerm) {
            abort(404);
        }
        return null;
    }

    public function index()
    {
        if ($r = $this->ensureAuthAndPerm('gestion.candidato.*')) { return $r; }
        return view('crud.candidato.ver');
    }

    public function create()
    {
        if ($r = $this->ensureAuthAndPerm('gestion.candidato.*')) { return $r; }
        return view('crud.candidato.crear');
    }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.candidato.*')) { return $r; }
        $data = $request->validate([
            'idParticipante' => 'required|integer',
            'idCargo' => 'required|integer',
            'idPartido' => 'required|integer',
        ]);
        $c = new Candidato($data);
        $c->save();
        return response()->json([
            'success' => true,
            'message' => 'Candidato creado',
            'data' => [
                'id' => $c->getKey(),
                'idParticipante' => $c->idParticipante,
                'idCargo' => $c->idCargo,
                'idPartido' => $c->idPartido,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.candidato.*')) { return $r; }
        $c = Candidato::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Candidato obtenido',
            'data' => [
                'idParticipante' => $c->idParticipante,
                'idCargo' => $c->idCargo,
                'idPartido' => $c->idPartido,
            ],
        ]);
    }

    public function edit($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.candidato.*')) { return $r; }
        return view('crud.candidato.editar');
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.candidato.*')) { return $r; }
        $c = Candidato::findOrFail($id);
        $data = $request->validate([
            'idParticipante' => 'required|integer',
            'idCargo' => 'required|integer',
            'idPartido' => 'required|integer',
        ]);
        $c->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Candidato actualizado',
            'data' => [
                'id' => $c->getKey(),
                'idParticipante' => $c->idParticipante,
                'idCargo' => $c->idCargo,
                'idPartido' => $c->idPartido,
            ],
        ]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.candidato.*')) { return $r; }
        $c = Candidato::findOrFail($id);
        $c->delete();
        return response()->json([
            'success' => true,
            'message' => 'Candidato eliminado',
            'data' => [
                'id' => (int) $id,
                'idParticipante' => $c->idParticipante,
                'idCargo' => $c->idCargo,
                'idPartido' => $c->idPartido,
            ],
        ]);
    }
}
