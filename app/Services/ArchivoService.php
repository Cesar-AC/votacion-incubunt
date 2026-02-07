<?php

namespace App\Services;

use App\Interfaces\Services\IArchivoService;
use App\Models\Archivo;
use Illuminate\Http\UploadedFile;

class ArchivoService implements IArchivoService
{
    public function obtenerArchivoPorId(int $id): Archivo
    {
        return Archivo::findOrFail($id);
    }

    public function subirArchivo(string $directorio, string $nombre, UploadedFile $archivo, string $disco = 'private'): Archivo
    {
        $ruta = $archivo->storeAs($directorio, $nombre, $disco);

        return Archivo::create([
            'disco' => $disco,
            'ruta' => $ruta,
            'mime' => $archivo->getMimeType(),
            'tamaño' => $archivo->getSize(),
        ]);
    }

    public function eliminarArchivo(int $id): void
    {
        $archivo = $this->obtenerArchivoPorId($id);
        $archivo->delete();
    }
}
