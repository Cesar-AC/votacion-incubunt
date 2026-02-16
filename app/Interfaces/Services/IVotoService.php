<?php

namespace App\Interfaces\Services;

use App\Models\Elecciones;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\User;
use Illuminate\Support\Collection;

interface IVotoService
{
    /**
     * Registra votos para un conjunto de candidatos y hasta un partido.
     * @param User $votante
     *      Obligatorio.
     *      Usuario que realiza el voto.
     * @param Collection<IElegibleAVoto> $entidades
     *      Obligatorio.
     *      Entidades al que se le vota.
     * @return void
     * 
     * @throws Exception
     *      - Se lanzará una excepción en los siguientes casos:
     *      - Si la elección no está activa.
     *      - Si el usuario no tiene permiso para votar.
     *      - Si el usuario no está registrado en el padrón electoral de la elección.
     *      - Si la entidad no pertenece a la elección.
     *      - Si el usuario ya ha votado.
     */
    public function votar(User $votante, Collection $entidades): void;

    /**
     * @param IElegibleAVoto $entidad
     *      Obligatorio.
     *      La entidad de la que se desea contar votos.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return int
     *      Retorna la cantidad de votos de la entidad.
     */
    public function contarVotos(IElegibleAVoto $entidad, ?Elecciones $eleccion = null): int;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      Usuario que realiza el voto.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Elección en la que se desea verificar si el usuario ha votado.
     *      Si no se especifica, se utilizará la elección activa.
     * @return bool
     *      Retorna true si el usuario ha votado en la elección, false en caso contrario.
     */
    public function haVotado(User $usuario, ?Elecciones $eleccion = null): bool;

    /**
     * @param User $votante
     *      Obligatorio.
     *      Usuario que realiza el voto.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Elección en la que se desea verificar si el usuario puede votar.
     *      Si no se especifica, se utilizará la elección activa.
     * @return bool
     *      Retorna true si el usuario puede votar en la elección, false en caso contrario.
     */
    public function puedeVotar(User $votante, ?Elecciones $eleccion = null): bool;
}
