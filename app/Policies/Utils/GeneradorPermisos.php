<?php

namespace App\Policies\Utils;

use Illuminate\Auth\Access\Response;

class GeneradorPermisos
{
    public const PERMISO_COMODIN = '*';

    public static function enBaseA(string $nombreModelo, ?string $tipoAccion, ?string $accion, ?string $idAccionable) {

        $nombreModelo = $nombreModelo ?? self::PERMISO_COMODIN;
        $tipoAccion = $tipoAccion ?? self::PERMISO_COMODIN;
        $accion = $accion ?? self::PERMISO_COMODIN;
        $idAccionable = $idAccionable ?? self::PERMISO_COMODIN;

        return join(":", [
            $nombreModelo,
            $tipoAccion,
            $accion,
            $idAccionable
        ]);
    }
}