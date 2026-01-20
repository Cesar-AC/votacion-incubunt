<?php

namespace App\Services;

use App\Interfaces\Services\IPermisoService;
use App\Models\Permiso;
use App\Models\User;
use App\Models\Rol;
use App\Models\RolPermiso;
use App\Models\RolUser;
use App\Models\UserPermiso;

class PermisoService implements IPermisoService
{
    public function comprobarUsuario(User $usuario, Permiso $permiso, bool $estricto = false): bool
    {
        $poseePermisoUsuario = UserPermiso::where('idUser', '=', $usuario->getKey())
            ->where('idPermiso', '=', $permiso->getKey())
            ->exists();

        if ($estricto) {
            return $poseePermisoUsuario;
        }

        $poseePermisoRol = User::where('idUser', '=', $usuario->getKey())
            ->whereHas('roles.permisos', function ($query) use ($permiso) {
                $query->where('Permiso.idPermiso', '=', $permiso->getKey());
            })
            ->exists();

        return $poseePermisoUsuario || $poseePermisoRol;
    }

    public function comprobarRol(Rol $rol, Permiso $permiso): bool
    {
        return RolPermiso::where('idRol', '=', $rol->getKey())
            ->where('idPermiso', '=', $permiso->getKey())
            ->exists();
    }

    public function perteneceUsuarioARol(User $usuario, Rol $rol): bool
    {
        return RolUser::where('idUser', '=', $usuario->getKey())
            ->where('idRol', '=', $rol->getKey())
            ->exists();
    }

    public function agregarPermisoAUsuario(User $usuario, Permiso $permiso): void
    {
        $usuario->permisos()->attach($permiso->getKey());
    }

    public function quitarPermisoDeUsuario(User $usuario, Permiso $permiso): void
    {
        $usuario->permisos()->detach($permiso->getKey());
    }

    public function agregarPermisoARol(Rol $rol, Permiso $permiso): void
    {
        $rol->permisos()->attach($permiso->getKey());
    }

    public function quitarPermisoDeRol(Rol $rol, Permiso $permiso): void
    {
        $rol->permisos()->detach($permiso->getKey());
    }

    public function agregarUsuarioARol(User $usuario, Rol $rol): void
    {
        $usuario->roles()->attach($rol->getKey());
    }

    public function quitarUsuarioDeRol(User $usuario, Rol $rol): void
    {
        $usuario->roles()->detach($rol->getKey());
    }

    public function permisoDesdeEnum(\App\Enum\Permiso $permisoEnum): Permiso
    {
        return Permiso::desdeEnum($permisoEnum);
    }
}
