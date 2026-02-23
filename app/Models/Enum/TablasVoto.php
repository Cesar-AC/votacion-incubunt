<?php

namespace App\Models\Enum;

enum TablasVoto: string
{
    case CANDIDATO = 'VotoCandidato';
    case PARTIDO = 'VotoPartido';
}
