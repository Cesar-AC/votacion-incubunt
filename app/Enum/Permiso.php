<?php

namespace App\Enum;

enum Permiso: string
{
    case VOTO_VOTAR = "voto:votar";
    case PROPUESTA_CANDIDATO_CRUD = "propuesta_candidato:crud:*";
    case PROPUESTA_PARTIDO_CRUD = "propuesta_partido:crud:*";
}
