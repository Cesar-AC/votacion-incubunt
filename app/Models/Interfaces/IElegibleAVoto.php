<?php

namespace App\Models\Interfaces;

use App\Models\TipoVoto;
use App\Models\User;

interface IElegibleAVoto
{
    public function obtenerTablaDeVoto(): string;

    public function obtenerPK(): mixed;

    public function obtenerTabla(): string;

    public function obtenerNombrePK(): string;

    public function obtenerTipoVoto(User $votante): TipoVoto;
}
