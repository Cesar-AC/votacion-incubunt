<?php

namespace App\Models\Traits;

use App\Models\TipoVoto;
use App\Models\User;

/**
 * Trait que define los metodos basicos para que un modelo pueda ser elegible para votar.
 * Requiere:
 * $tablaVoto
 */
trait ElegibleAVoto
{
    public function obtenerTablaDeVoto(): string
    {
        if (empty($this->tablaVoto)) {
            throw new \Exception('La tabla de voto no ha sido definida.');
        }

        return $this->tablaVoto;
    }

    public function obtenerPK(): mixed
    {
        return $this->getKey();
    }

    public function obtenerTabla(): string
    {
        return $this->table;
    }

    public function obtenerNombrePK(): string
    {
        return $this->primaryKey;
    }

    public function obtenerTipoVoto(User $votante): TipoVoto
    {
        return TipoVoto::noAplicable();
    }
}
