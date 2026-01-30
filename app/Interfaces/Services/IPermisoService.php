<?php

namespace App\Interfaces\Services;

use App\Models\Permiso;
use App\Models\User;
use App\Models\Rol;

/**
 * Interfaz que define los métodos para el manejo de permisos.
 * @package App\Interfaces\Services
 */
interface IPermisoService
{
    /**
     * Comprueba si un usuario tiene un permiso específico.
     * @param User $usuario
     *      Obligatorio.
     *      Usuario que se desea verificar.
     * @param Permiso $permiso
     *      Obligatorio.
     *      Permiso que se desea verificar.
     * @param bool $estricto
     *      Opcional.
     *      Por defecto, verificará si el usuario posee el permiso directamente o a través de uno o más roles.
     *      Si se establece en true, solo se verificará si el usuario posee el permiso directamente.
     * @return bool
     *      Retorna true si el usuario tiene el permiso, false en caso contrario.
     */
    public function comprobarUsuario(User $usuario, Permiso $permiso, bool $estricto = false): bool;

    /**
     * Comprueba si un rol tiene un permiso específico.
     * @param Rol $rol
     *      Obligatorio.
     *      Rol que se desea verificar.
     * @param Permiso $permiso
     *      Obligatorio.
     *      Permiso que se desea verificar.
     * @return bool
     *      Retorna true si el rol tiene el permiso, false en caso contrario.
     */
    public function comprobarRol(Rol $rol, Permiso $permiso): bool;

    /**
     * Verifica si un usuario pertenece a un rol.
     * @param User $usuario
     *      Obligatorio.
     *      Usuario que se desea verificar.
     * @param Rol $rol
     *      Obligatorio.
     *      Rol que se desea verificar.
     * @return bool
     *      Retorna true si el usuario pertenece al rol, false en caso contrario.
     */
    public function perteneceUsuarioARol(User $usuario, Rol $rol): bool;

    /**
     * Agrega un permiso a un usuario.
     * @param User $usuario
     *      Obligatorio.
     *      Usuario al que se le agregará el permiso.
     * @param Permiso $permiso
     *      Obligatorio.
     *      Permiso que se agregará al usuario.
     * @return void
     */
    public function agregarPermisoAUsuario(User $usuario, Permiso $permiso): void;

    /**
     * Agrega un permiso a un rol.
     * @param Rol $rol
     *      Obligatorio.
     *      Rol al que se le agregará el permiso.
     * @param Permiso $permiso
     *      Obligatorio.
     *      Permiso que se agregará al rol.
     * @return void
     */
    public function agregarPermisoARol(Rol $rol, Permiso $permiso): void;

    /**
     * Agrega un usuario a un rol.
     * @param User $usuario
     *      Obligatorio.
     *      Usuario que se agregará al rol.
     * @param Rol $rol
     *      Obligatorio.
     *      Rol al que se le agregará el usuario.
     * @return void
     */
    public function agregarUsuarioARol(User $usuario, Rol $rol): void;

    /**
     * Quita un permiso de un usuario.
     * @param User $usuario
     *      Obligatorio.
     *      Usuario al que se le quitará el permiso.
     * @param Permiso $permiso
     *      Obligatorio.
     *      Permiso que se quitará al usuario.
     * @return void
     */
    public function quitarPermisoDeUsuario(User $usuario, Permiso $permiso): void;

    /**
     * Quita un permiso de un rol.
     * @param Rol $rol
     *      Obligatorio.
     *      Rol al que se le quitará el permiso.
     * @param Permiso $permiso
     *      Obligatorio.
     *      Permiso que se quitará al rol.
     * @return void
     */
    public function quitarPermisoDeRol(Rol $rol, Permiso $permiso): void;

    /**
     * Quita un usuario de un rol.
     * @param User $usuario
     *      Obligatorio.
     *      Usuario que se quitará del rol.
     * @param Rol $rol
     *      Obligatorio.
     *      Rol del que se le quitará el usuario.
     * @return void
     */
    public function quitarUsuarioDeRol(User $usuario, Rol $rol): void;

    /**
     * Obtiene un permiso desde un enum.
     * @param \App\Enum\Permiso $permisoEnum
     *      Obligatorio.
     *      Enum de permiso que se desea obtener.
     * @return Permiso
     *      Retorna el permiso.
     */
    public function permisoDesdeEnum(\App\Enum\Permiso $permisoEnum): Permiso;

    /**
     * Obtiene un permiso desde su ID.
     * @param int $idPermiso
     *      Obligatorio.
     *      ID del permiso que se desea obtener.
     * @return Permiso
     *      Retorna el permiso.
     */
    public function obtenerPermisoPorId(int $idPermiso): Permiso;
}
