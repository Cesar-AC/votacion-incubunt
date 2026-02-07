<?php

namespace App\Interfaces\Services;

use App\Models\Elecciones;
use Illuminate\Support\Collection;
use App\Models\User;

interface IPadronElectoralService
{
    /**
     * @param Elecciones|null $eleccion
     *      Opcional.
     *      Si es enviado, se obtendrá el padrón electoral para la elección especificada.
     *      Si no es enviado, se obtendrá el padrón electoral de la elección activa.
     * @return Collection<User>
     *      Retorna una colección de usuarios.
     */
    public function obtenerPadronElectoral(?Elecciones $eleccion = null): Collection;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario que se desea agregar al padrón electoral.
     * @param Elecciones|null $eleccion
     *      Opcional.
     *      Si es enviado, se agregará el usuario al padrón electoral de la elección especificada.
     *      Si no es enviado, se agregará el usuario al padrón electoral de la elección activa.
     * @return void
     * @throws \Exception Si el usuario ya pertenece al padrón electoral.
     */
    public function agregarUsuarioAEleccion(User $usuario, ?Elecciones $eleccion = null): void;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario que se desea eliminar del padrón electoral.
     * @param Elecciones|null $eleccion
     *      Opcional.
     *      Si es enviado, se eliminará el usuario del padrón electoral de la elección especificada.
     *      Si no es enviado, se eliminará el usuario del padrón electoral de la elección activa.
     * @return void
     * @throws \Exception Si el usuario no pertenece al padrón electoral.
     */
    public function eliminarUsuarioDeElecciones(User $usuario, ?Elecciones $eleccion = null): void;

    /**
     * @param Elecciones|null $eleccion
     *      Opcional.
     *      Si es enviado, se restablecerá el padrón electoral de la elección especificada.
     *      Si no es enviado, se restablecerá el padrón electoral de la elección activa.
     * @return void
     */
    public function restablecerPadronElectoral(?Elecciones $eleccion = null): void;
}
