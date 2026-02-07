<?php

namespace App\Models\Traits;

use App\Models\Archivo;
use Illuminate\Support\Facades\Storage;

trait TieneFoto
{
    public function tieneFoto(): bool
    {
        $campoFoto = $this->campoFoto ?? 'foto_idArchivo';
        return $this->{$campoFoto} !== null;
    }
    public function obtenerFotoURL(): ?string
    {
        $campoFoto = $this->campoFoto ?? 'foto_idArchivo';
        $archivo = Archivo::where('idArchivo', '=', $this->{$campoFoto})->first();

        return $archivo ? Storage::url($archivo->ruta) : null;
    }
}
