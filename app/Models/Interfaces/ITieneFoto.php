<?php

namespace App\Models\Interfaces;

interface ITieneFoto
{
    /**
     * Obtiene la URL de la foto del perfil vinculado.
     * - Retorna null si no tiene foto o si no se encuentra el archivo.
     *
     * @return string|null
     */
    public function obtenerFotoURL(): ?string;
}
