<?php

namespace App\Enum;

enum Permiso: string
{
    case VOTO_VOTAR = "voto:votar";
    case PROPUESTA_CANDIDATO_CRUD = "propuesta_candidato:crud:*";
    case PROPUESTA_PARTIDO_CRUD = "propuesta_partido:crud:*";
    case PERFIL_EDITAR = "perfil:editar";
    case USUARIO_CAMBIAR_FOTO = "usuario:cambiar_foto";
}
