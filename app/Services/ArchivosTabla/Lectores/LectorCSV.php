<?php

namespace App\Services\ArchivosTabla\Lectores;

use App\Interfaces\Services\ArchivosTabla\ILectorArchivoTabla;
use Iterator;
use League\Csv\Reader;

class LectorCSV implements ILectorArchivoTabla
{
    public function __construct(protected bool $hasHeader = true) {}

    public function leer(string $ruta): Iterator
    {
        $csv = Reader::createFromPath($ruta, 'r');
        if ($this->hasHeader) $csv->setHeaderOffset(0);

        return $csv->getRecords();
    }
}
