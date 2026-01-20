<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PerfilUsuario;
use App\Models\RolUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['perfil', 'roles'])->get();

        return view('crud.user.ver', compact('usuarios'));
    }

    public function create()
    {
        $carreras = \App\Models\Carrera::all();
        $areas = \App\Models\Area::all();
        return view('crud.user.crear', compact('carreras', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // USER
            'correo' => 'required|email|unique:User,correo',
            'password' => 'required|min:6',
            'idEstadoUsuario' => 'required|exists:EstadoUsuario,idEstadoUsuario',

            // PERFIL
            'apellidoPaterno' => 'required|string',
            'apellidoMaterno' => 'required|string',
            'nombre' => 'required|string',
            'dni' => 'required|max:8',

            // ROL
            'idRol' => 'required|exists:Rol,idRol',
        ]);

        $user = null;

        try {
            DB::transaction(function () use ($request, &$user) {

                // 1️⃣ USER
                $user = User::create([
                    'correo' => $request->correo,
                    'contraseña' => Hash::make($request->password),
                    'idEstadoUsuario' => $request->idEstadoUsuario,
                ]);

                // 2️⃣ PERFIL
                PerfilUsuario::create([
                    'idUser' => $user->idUser,
                    'apellidoPaterno' => $request->apellidoPaterno,
                    'apellidoMaterno' => $request->apellidoMaterno,
                    'nombre' => $request->nombre,
                    'otrosNombres' => $request->otrosNombres,
                    'dni' => $request->dni,
                    'telefono' => $request->telefono,
                    'idCarrera' => $request->idCarrera,
                    'idArea' => $request->idArea,
                ]);

                // 3️⃣ ROL
                RolUser::create([
                    'idUser' => $user->idUser,
                    'idRol' => $request->idRol,
                ]);
            });

            return redirect()
                ->route('crud.user.ver')
                ->with('success', 'Usuario creado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al crear el usuario: ' . $e->getMessage()
            ])->withInput();
        }
    }


    public function show($id)
    {
        $u = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Usuario obtenido',
            'data' => [
                'correo' => $u->correo,
            ],
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $carreras = \App\Models\Carrera::all();
        $areas = \App\Models\Area::all();
        return view('crud.user.editar', compact('user', 'carreras', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $u = User::findOrFail($id);
        $data = $request->validate([
            'correo' => 'required|email|unique:User,correo,' . $u->getKey() . ',idUser',
        ]);
        $u->update($data);
        return redirect()
            ->route('crud.user.ver')
            ->with('success', 'Usuario actualizado correctamente');
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
                'correo' => $u->correo,
            ],
        ]);
    }
}
