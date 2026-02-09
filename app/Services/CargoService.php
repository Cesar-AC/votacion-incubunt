<?php

namespace App\Services;

use App\Interfaces\Services\ICargoService;
use App\Models\Area;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;
use App\Models\Elecciones;
use Illuminate\Support\Collection;

class CargoService implements ICargoService
{
    public function obtenerCargos(): Collection
    {
        return Cargo::get();
    }

    public function obtenerCargoPorId(int $id): Cargo
    {
        return Cargo::findOrFail($id);
    }

    public function crearCargo(array $datos): Cargo
    {
        return Cargo::create($datos);
    }

    public function editarCargo(array $datos, Cargo $cargo): Cargo
    {
        $cargo->update($datos);
        return $cargo;
    }

    public function eliminarCargo(Cargo $cargo): void
    {
        $cargo->delete();
    }

    public function obtenerCargosPorArea(Area $area): Collection
    {
        return Cargo::where('idArea', '=', $area->getKey())->get();
    }

    public function obtenerCargoDePostulacionDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): Cargo
    {
        return CandidatoEleccion::where('idCandidato', '=', $candidato->getKey())
            ->where('idElecciones', '=', $elecciones->getKey())
            ->first();
    }
}
