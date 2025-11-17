<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
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
        if ($r = $this->ensureAuthAndPerm('gestion.user.*')) { return $r; }
        return view('crud.user.ver');
    }

    public function create()
    {
        if ($r = $this->ensureAuthAndPerm('gestion.user.*')) { return $r; }
        return view('crud.user.crear');
    }

    public function store(Request $request)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.user.*')) { return $r; }
        $data = $request->validate([
            'usuario' => 'required|string|max:255',
            'email' => 'required|email|unique:User,email',
            'password' => 'required|string|min:6',
        ]);
        $u = new User($data);
        $u->save();
        return response()->json([
            'success' => true,
            'message' => 'Usuario creado',
            'data' => [
                'id' => $u->getKey(),
                'usuario' => $u->usuario,
                'email' => $u->email,
            ],
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.user.*')) { return $r; }
        $u = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Usuario obtenido',
            'data' => [
                'usuario' => $u->usuario,
                'email' => $u->email,
            ],
        ]);
    }

    public function edit($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.user.*')) { return $r; }
        return view('crud.user.editar');
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.user.*')) { return $r; }
        $u = User::findOrFail($id);
        $data = $request->validate([
            'usuario' => 'required|string|max:255',
            'email' => 'required|email|unique:User,email,' . $u->getKey() . ',idUser',
        ]);
        $u->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado',
            'data' => [
                'id' => $u->getKey(),
                'usuario' => $u->usuario,
                'email' => $u->email,
            ],
        ]);
    }

    public function destroy($id)
    {
        if ($r = $this->ensureAuthAndPerm('gestion.user.*')) { return $r; }
        $u = User::findOrFail($id);
        $u->delete();
        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado',
            'data' => [
                'id' => (int) $id,
                'usuario' => $u->usuario,
                'email' => $u->email,
            ],
        ]);
    }
}
