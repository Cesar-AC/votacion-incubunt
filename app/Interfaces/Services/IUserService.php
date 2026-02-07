<?php

namespace App\Interfaces\Services;

use App\Models\Archivo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface IUserService
{
    /**
     * @return Collection<User>
     *      Retorna la lista de usuarios.
     */
    public function obtenerUsuarios(): Collection;

    /**
     * @param int $id
     *      Obligatorio.
     *      El id del usuario que se desea obtener.
     * @return User
     *      Retorna el usuario con el id especificado.
     * @throws \Exception Si no se encuentra el usuario.
     */
    public function obtenerUsuarioPorId(int $id): User;

    /**
     * @param array $datosUsuario
     *      Obligatorio.
     *      Los datos de inicio de sesión del usuario que se desea crear.
     * @param array $datosPerfil
     *      Obligatorio.
     *      Los datos del perfil del usuario que se desea crear.
     * @return User
     *      Retorna el usuario creado.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function crearUsuario(array $datosUsuario, array $datosPerfil): User;

    /**
     * @param array $datosUsuario
     *      Obligatorio.
     *      Los nuevos datos de inicio de sesión que se desea asignar al usuario.
     * @param User $usuario
     *      Obligatorio.
     *      El usuario que se desea editar.
     * @return User
     *      Retorna el usuario editado.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function editarUsuario(array $datosUsuario, User $usuario): User;

    /**
     * @param array $datosPerfil
     *      Obligatorio.
     *      Los nuevos datos del perfil que se desea asignar al usuario.
     * @param User $usuario
     *      Obligatorio.
     *      El usuario que se desea editar.
     * @return User
     *      Retorna el usuario editado.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function editarPerfilUsuario(array $datosPerfil, User $usuario): User;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envía el usuario.
     */
    public function eliminarUsuario(User $usuario): void;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario al que se desea subir la foto.
     * @param UploadedFile $archivo
     *      Obligatorio.
     *      El archivo que se desea subir.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function subirFotoUsuario(User $usuario, UploadedFile $archivo): void;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario al que se desea remover la foto.
     * @return void
     * @throws \Exception Si no se envía el usuario.
     */
    public function removerFotoUsuario(User $usuario): void;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario al que se desea cambiar la foto.
     * @param UploadedFile $archivo
     *      Obligatorio.
     *      El archivo que se desea asignar como foto.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function cambiarFotoUsuario(User $usuario, UploadedFile $archivo): void;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario al que se desea obtener la foto.
     * @return string
     *      Retorna la URL pública de la foto del usuario.
     * @throws \Exception Si no se envía el usuario.
     */
    public function obtenerFotoUsuarioURL(User $usuario): string;
}
