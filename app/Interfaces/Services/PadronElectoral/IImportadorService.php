<?php

namespace App\Interfaces\Services\PadronElectoral;

use App\Models\Elecciones;

interface IImportadorService
{
    public function importar(string $path, Elecciones $elecciones);
}
