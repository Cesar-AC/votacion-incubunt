<?php

namespace App\Services;

use App\Interfaces\Services\IArchivoService;
use App\Interfaces\Services\IUserService;
use App\Models\Archivo;
use App\Models\EstadoUsuario;
use App\Models\PerfilUsuario;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService implements IUserService
{
    public function __construct(protected IArchivoService $archivoService) {}

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
        $usuario->update([
            'idEstadoUsuario' => EstadoUsuario::INHABILITADO,
        ]);
    }

    public function subirFoto(User $usuario, UploadedFile $archivo): void
    {
        if ($usuario->perfil?->foto) {
            throw new \Exception('El usuario ya tiene una foto.');
        }

        $archivo = $this->archivoService->subirArchivo('usuarios/fotos', $archivo->hashName(), $archivo, 'public');

        $usuario->perfil->foto()->associate($archivo);
        $usuario->perfil->save();
    }

    public function removerFoto(User $usuario): void
    {
        $foto = $usuario->perfil?->foto;

        $usuario->perfil?->foto()->disassociate();
        $usuario->perfil?->save();

        if ($foto == null) {
            throw new \Exception('El usuario no tiene una foto.');
        }

        $this->archivoService->eliminarArchivo($foto->getKey());
    }

    public function cambiarFoto(User $usuario, UploadedFile $archivo): void
    {
        try {
            $this->removerFoto($usuario);
        } catch (\Exception $e) {
            // Ignorar, puede que no tenga foto.
        }

        $this->subirFoto($usuario, $archivo);
    }

    public function obtenerFotoURL(User $usuario): string
    {
        return $usuario->perfil->obtenerFotoURL();
    }

    public function restaurarUsuario(User $usuario): void
    {
        $usuario->update([
            'idEstadoUsuario' => EstadoUsuario::ACTIVO,
        ]);
    }
}
