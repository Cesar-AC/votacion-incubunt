<?php

namespace App\Services;

use App\Interfaces\Services\ICarreraService;
use App\Models\Carrera;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class CarreraService implements ICarreraService
{
    public function obtenerCarreras(): Collection
    {
        return Carrera::all();
    }

    public function obtenerCarreraPorId(int $id): Carrera
    {
        return Carrera::findOrFail($id);
    }

    public function crearCarrera(array $datos): Carrera
    {
        return Carrera::create($datos);
    }

    public function editarCarrera(array $datos, Carrera $carrera): Carrera
    {
        $carrera->update($datos);

        return $carrera;
    }

    public function eliminarCarrera(Carrera $carrera): void
    {
        $carrera->delete();
    }
}
