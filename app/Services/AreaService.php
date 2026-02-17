<?php

namespace App\Services;

use App\Interfaces\Services\IAreaService;
use App\Models\Area;
use App\Models\Candidato;
use App\Models\Elecciones;
use Illuminate\Support\Collection;

class AreaService implements IAreaService
{
    public function __construct(protected EleccionesService $eleccionesService) {}

    public function obtenerTodasLasAreas(): Collection
    {
        return Area::all();
    }

    public function obtenerAreas(): Collection
    {
        return Area::where('idArea', '!=', Area::PRESIDENCIA)
            ->where('idArea', '!=', Area::SIN_AREA_ASIGNADA)
            ->get();
    }

    public function obtenerAreaPorId(int $id): Area
    {
        return Area::findOrFail($id);
    }

    public function crearArea(array $datos): Area
    {
        return Area::create($datos);
    }

    public function editarArea(array $datos, Area $area): Area
    {
        $area->update($datos);

        return $area;
    }

    public function eliminarArea(Area $area): void
    {
        $area->delete();
    }

    public function obtenerAreaDeCandidato(Candidato $candidato): Area
    {
        return $candidato->usuario->perfil->area;
    }

    public function obtenerAreaDePostulacionDeCandidato(Candidato $candidato, ?Elecciones $elecciones = null): Area
    {
        $elecciones = $elecciones ?? $this->eleccionesService->obtenerEleccionActiva();

        return $this->eleccionesService->obtenerCandidatoEleccion($candidato, $elecciones)->cargo->area;
    }
}
