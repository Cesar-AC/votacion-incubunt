<?php

namespace App\Policies\Utils;

use App\Models\User;

class ValidadorPermisos
{
    private static function validarPermisos($permisos_usuario, $permisos_validos){
        $intersect = array_intersect($permisos_usuario, $permisos_validos);

        return count($intersect) > 0;
    }

    public static function obtenerExcepcionesPermisosDeUsuario(User $usuario){
        $excepciones = $usuario->excepciones_permisos()->pluck('permiso');
        return $excepciones
            ->unique()
            ->values()
            ->all();
    }

    public static function obtenerPermisosDeRolesDeUsuario(User $usuario){
        $roles = $usuario->roles()->with('permisos')->get();
        return $roles
            ->flatMap(function ($rol) { return $rol->permisos->pluck('permiso'); })
            ->unique()
            ->values()
            ->all();
    }

    public static function obtenerTodosLosPermisosDeUsuario(User $usuario){
        $permisos_usuario = $usuario->permisos()->pluck('permiso');
        $permisos_roles = collect(self::obtenerPermisosDeRolesDeUsuario($usuario));
        $permisos_excepciones = collect(self::obtenerExcepcionesPermisosDeUsuario($usuario));

        $permisos = $permisos_usuario
            ->merge($permisos_roles)
            ->unique()
            ->values();

        $permisos = $permisos->diff($permisos_excepciones);

        return $permisos->all();
    }

    public static function usuarioTienePermiso(User $usuario, string $permiso){
        $permisos_usuario = self::obtenerTodosLosPermisosDeUsuario($usuario);
        return self::validarPermisos($permisos_usuario, [$permiso]);
    }

    public static function usuarioTienePermisos(User $usuario, array $permisos){
        $permisos_usuario = self::obtenerTodosLosPermisosDeUsuario($usuario);
        return self::validarPermisos($permisos_usuario, $permisos);
    }
}
