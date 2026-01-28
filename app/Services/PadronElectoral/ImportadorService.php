<?php

namespace App\Services\PadronElectoral;

use App\Enum\ImportadorArchivo;
use App\Interfaces\Services\PadronElectoral\IImportadorFactory;
use App\Interfaces\Services\PadronElectoral\IImportadorService;
use App\Models\Elecciones;

class ImportadorService implements IImportadorService
{
    public function __construct(
        protected IImportadorFactory $importadorFactory
    ) {}

    public function importar(string $path, Elecciones $elecciones)
    {
        $importador = match (true) {
            str_ends_with($path, '.csv') => $this->importadorFactory->crear(ImportadorArchivo::CSV),
            str_ends_with($path, '.xlsx') => $this->importadorFactory->crear(ImportadorArchivo::XLSX),
        };

        $importador->importar($path, $elecciones);
    }
}
