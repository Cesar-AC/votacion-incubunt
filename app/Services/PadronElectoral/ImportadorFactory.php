<?php

namespace App\Services\PadronElectoral;

use App\Enum\ImportadorArchivo;
use App\Interfaces\Services\PadronElectoral\IImportadorFactory;
use App\Interfaces\Services\PadronElectoral\IImportarDesdeArchivo;
use App\Services\ArchivosTabla\Lectores\LectorCSV;
use App\Services\ArchivosTabla\Lectores\LectorXLSX;

class ImportadorFactory implements IImportadorFactory
{
    public function crear(ImportadorArchivo $tipo): IImportarDesdeArchivo
    {
        return match ($tipo) {
            ImportadorArchivo::XLSX => new ImportarDesdeArchivo(new LectorXLSX(hasHeader: true)),
            ImportadorArchivo::CSV => new ImportarDesdeArchivo(new LectorCSV(hasHeader: true)),
            default => throw new \Exception('Tipo de archivo no soportado'),
        };
    }
}
