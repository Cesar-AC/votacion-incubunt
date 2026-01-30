<?php

namespace App\Services;

use App\Interfaces\Services\IUserService;
use App\Models\EstadoUsuario;
use App\Models\PerfilUsuario;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserService implements IUserService
{
    public function obtenerUsuarios(): Collection
    {
        return User::all();
    }

    public function obtenerUsuarioPorId(int $id): User
    {
        return User::findOrFail($id);
    }

    public function crearUsuario(array $datosUsuario, array $datosPerfil): User
    {
        $datosUsuario['idEstadoUsuario'] = EstadoUsuario::ACTIVO;

        /** @var User $usuario */
        $usuario = null;
        DB::transaction(function () use ($datosUsuario, $datosPerfil, &$usuario) {
            $usuario = User::create([
                'correo' => $datosUsuario['correo'],
                'contraseña' => bcrypt($datosUsuario['contraseña']),
                'idEstadoUsuario' => $datosUsuario['idEstadoUsuario'],
            ]);

            $datosPerfil['idUser'] = $usuario->getKey();

            PerfilUsuario::create($datosPerfil);
        });

        if ($usuario == null) throw new \Exception('Error al crear el usuario');

        return $usuario;
    }

    public function editarUsuario(array $datosUsuario, User $usuario): User
    {
        $usuario->update($datosUsuario);

        return $usuario;
    }

    public function editarPerfilUsuario(array $datosPerfil, User $usuario): User
    {
        $usuario->perfil()->update($datosPerfil);

        return $usuario;
    }

    public function eliminarUsuario(User $usuario): void
    {
        if ($usuario->perfil) {
            $usuario->perfil()->delete();
        }
        $usuario->delete();
    }
}
