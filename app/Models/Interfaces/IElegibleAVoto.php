<?php

namespace App\Models\Interfaces;

use App\Models\TipoVoto;
use App\Models\User;

/**
 * Interfaz que define los métodos comunes para los modelos que pueden ser votados.
 */
interface IElegibleAVoto
{
    /**
     * @return string
     *      Retorna el nombre de la tabla de votos.
     */
    public function obtenerTablaDeVoto(): string;

    /**
     * @return mixed
     *      Retorna el valor de la primary key.
     */
    public function obtenerPK(): mixed;

    /**
     * @return string
     *      Retorna el nombre de la tabla.
     */
    public function obtenerTabla(): string;

    /**
     * @return string
     *      Retorna el nombre de la primary key.
     */
    public function obtenerNombrePK(): string;

    /**
     * @param User $votante
     *      Obligatorio.
     *      El votante que se desea obtener.
     * @return TipoVoto
     *      Retorna el tipo de voto.
     */
    public function obtenerTipoVoto(User $votante): TipoVoto;
}
