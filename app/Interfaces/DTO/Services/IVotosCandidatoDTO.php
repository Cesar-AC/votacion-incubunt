<?php

namespace App\Interfaces\DTO\Services;

use App\Models\Candidato;

interface IVotosCandidatoDTO
{
    /**
     * @param Candidato $candidato
     *      El candidato que se busca guardar en el DTO.
     * @param int $votos
     *      El número de votos que se busca guardar en el DTO.
     */
    public function __construct(Candidato $candidato, int $votos);

    /**
     * @return Candidato
     *      Permite obtener el candidato registrado.
     */
    public function getCandidato(): Candidato;

    /**
     * @return int
     *      Permite obtener el número de votos registrado.
     */
    public function getVotos(): int;
}
