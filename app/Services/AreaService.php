<?php

namespace App\Services;

use App\Interfaces\Services\IAreaService;
use App\Models\Area;
use Illuminate\Support\Collection;

class AreaService implements IAreaService
{
    public function obtenerAreas(): Collection
    {
        return Area::where('idArea', '!=', Area::SIN_AREA_ASIGNADA)->get();
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
}
