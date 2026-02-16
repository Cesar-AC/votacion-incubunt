<?php

namespace App\DTO\Services;

use App\Interfaces\DTO\Services\IVotosCandidatoDTO;
use App\Models\Candidato;

class VotosCandidatoDTO implements IVotosCandidatoDTO
{
    public function __construct(protected Candidato $candidato, protected int $votos) {}

    public function getCandidato(): Candidato
    {
        return $this->candidato;
    }

    public function getVotos(): int
    {
        return $this->votos;
    }
}
