<?php

namespace App\Interfaces\Services\PadronElectoral;

use App\Enum\ImportadorArchivo;

interface IImportadorFactory
{
    public function crear(ImportadorArchivo $tipo): IImportarDesdeArchivo;
}
