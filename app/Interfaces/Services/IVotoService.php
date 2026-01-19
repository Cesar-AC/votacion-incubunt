<?php

namespace App\Interfaces\Services;

use App\Models\Candidato;
use App\Models\User;

interface IVotoService
{
    /**
     * Registra un voto para un candidato.
     * @param User $usuario
     *      Obligatorio.
     *      Usuario que realiza el voto.
     * @param Candidato $candidato
     *      Obligatorio.
     *      Candidato al que se le vota.
     * @return void
     * 
     * @throws Exception
     *      - Se lanzará una excepción en los siguientes casos:
     *      - Si la elección no está activa.
     *      - Si el usuario no tiene permiso para votar.
     *      - Si el usuario no está registrado en el padrón electoral de la elección.
     *      - Si el candidato no pertenece a la elección.
     *      - Si el usuario ya ha votado.
     */
    public function votar(User $usuario, Candidato $candidato): void;
}
