<?php

namespace App\Interfaces\Services\ArchivosTabla;

use Iterator;

/**
 * Interfaz para leer archivos de tablas
 */
interface ILectorArchivoTabla
{
    /**
     * Lee un archivo de tabla y devuelve un iterador sobre el mismo.
     *
     * @param string $ruta
     * @return Iterator
     */
    public function leer(string $ruta): Iterator;
}
