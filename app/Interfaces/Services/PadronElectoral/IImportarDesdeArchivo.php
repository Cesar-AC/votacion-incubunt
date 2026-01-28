<?php

namespace App\Interfaces\Services\PadronElectoral;

use App\Models\Elecciones;

/**
 * Importa un archivo CSV con datos de padron electoral.
 */
interface IImportarDesdeArchivo
{
    /**
     * Importa un archivo CSV con datos de padron electoral.
     * 
     * @param string $path Ruta del archivo CSV.
     * @param Elecciones $eleccion Elección a la que se importarán los datos.
     */
    public function importar(string $path, Elecciones $eleccion): void;
}
