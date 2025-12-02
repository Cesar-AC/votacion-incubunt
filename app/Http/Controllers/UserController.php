<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('crud.user.ver');
    }

    public function create()
    {
        return view('crud.user.crear');
    }

    public function store(Request $request)
    {
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
        return view('crud.user.editar');
    }

    public function update(Request $request, $id)
    {
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
