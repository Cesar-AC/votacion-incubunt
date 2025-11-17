<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CargoController extends Controller
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
        if ($r = $this->ensureAuthAndPerm('gestion.cargo.*')) { return $r; }
        return view('crud.cargo.ver');
    }

    public function create()
    {
        if ($r = $this->ensureAuthAndPerm('gestion.cargo.*')) { return $r; }
        return view('crud.cargo.crear');
    }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.cargo.*')) { return $r; }
        $data = $request->validate([
            'idCargo' => 'required|integer',
            'cargo' => 'required|string|max:100',
            'idArea' => 'required|integer',
        ]);
        $c = new Cargo($data);
        $c->save();
        return response()->json([
            'success' => true,
            'message' => 'Cargo creado',
            'data' => [
                'id' => $c->getKey(),
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.cargo.*')) { return $r; }
        $c = Cargo::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Cargo obtenido',
            'data' => [
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ]);
    }

    public function edit($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.cargo.*')) { return $r; }
        return view('crud.cargo.editar');
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.cargo.*')) { return $r; }
        $c = Cargo::findOrFail($id);
        $data = $request->validate([
            'cargo' => 'required|string|max:100',
            'idArea' => 'required|integer',
        ]);
        $c->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Cargo actualizado',
            'data' => [
                'id' => $c->getKey(),
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.cargo.*')) { return $r; }
        $c = Cargo::findOrFail($id);
        $c->delete();
        return response()->json([
            'success' => true,
            'message' => 'Cargo eliminado',
            'data' => [
                'id' => (int) $id,
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ]);
    }
}
