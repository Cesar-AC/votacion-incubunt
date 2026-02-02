<?php

namespace App\Interfaces\Services;

use App\Models\Elecciones;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\User;

interface IVotoService
{
    /**
     * Registra un voto para un candidato.
     * @param User $votante
     *      Obligatorio.
     *      Usuario que realiza el voto.
     * @param IElegibleAVoto $entidad
     *      Obligatorio.
     *      Entidad al que se le vota.
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
    public function votar(User $votante, IElegibleAVoto $entidad): void;

    /**
     * @param IElegibleAVoto $entidad
     *      Obligatorio.
     *      La entidad que se desea obtener votos.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return int
     *      Retorna la cantidad de votos de la entidad.
     */
    public function contarVotos(IElegibleAVoto $entidad, ?Elecciones $eleccion = null): int;
}
