<?php

namespace App\Services\ArchivosTabla\Lectores;

use App\Interfaces\Services\ArchivosTabla\ILectorArchivoTabla;
use Aspera\Spreadsheet\XLSX\Reader;
use Iterator;

class LectorXLSX implements ILectorArchivoTabla
{
    public function __construct(
        protected bool $hasHeader = true
    ) {}

    public function leer(string $ruta): Iterator
    {
        $lector = new Reader();
        $lector->open($ruta);
        $lector->changeSheet(0);

        return (function () use ($lector) {
            $headers = null;

            foreach ($lector as $row) {
                if ($this->hasHeader && $headers === null) {
                    $headers = array_map(function ($header) {
                        $header = trim($header);
                        $header = mb_strtolower($header, 'UTF-8');

                        return $header;
                    }, $row);
                    continue;
                }

                if ($headers) {
                    $len = count($headers);
                    $row = array_slice($row, 0, $len);
                    if (count($row) < $len) {
                        $row = array_pad($row, $len, null);
                    }
                    yield array_combine($headers, $row);
                } else {
                    yield $row;
                }
            }
        })();
    }
}
