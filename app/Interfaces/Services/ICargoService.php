<?php

namespace App\Interfaces\Services;

use App\Models\Area;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Elecciones;
use Illuminate\Support\Collection;

interface ICargoService
{
    public function obtenerCargos(): Collection;

    public function obtenerCargoPorId(int $id): Cargo;

    public function crearCargo(array $datos): Cargo;

    public function editarCargo(array $datos, Cargo $cargo): Cargo;

    public function eliminarCargo(Cargo $cargo): void;

    public function obtenerCargosPorArea(Area $area): Collection;

    public function obtenerCargoDePostulacionDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): Cargo;
}
