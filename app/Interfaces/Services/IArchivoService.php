<?php

namespace App\Interfaces\Services;

use App\Models\Archivo;
use Illuminate\Http\UploadedFile;

interface IArchivoService
{
    /**
     * @param int $id
     *      Obligatorio.
     *      El id del archivo que se desea obtener.
     * @return Archivo
     *      Retorna el archivo con el id especificado.
     * @throws \Exception Si no se encuentra el archivo.
     */
    public function obtenerArchivoPorId(int $id): Archivo;

    /**
     * @param string $directorio
     *      Obligatorio.
     *      El directorio en el que se desea subir el archivo.
     * @param string $nombre
     *      Obligatorio.
     *      El nombre del archivo que se desea subir.
     * @param UploadedFile $archivo
     *      Obligatorio.
     *      El archivo que se desea subir.
     * @param string $disco
     *      Opcional.
     *      El disco en el que se desea subir el archivo. Por defecto es 'private'.
     * @return Archivo
     *      Retorna el archivo subido.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function subirArchivo(string $directorio, string $nombre, UploadedFile $archivo, string $disco = 'private'): Archivo;

    /**
     * @param int $id
     *      Obligatorio.
     *      El id del archivo que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envía el archivo.
     */
    public function eliminarArchivo(int $id): void;
}
