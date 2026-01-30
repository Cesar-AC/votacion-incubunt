<?php

namespace App\Services;

use App\Interfaces\IEstadisticasElectoralesService;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\PadronElectoral;
use App\Models\Area;

class EstadisticasElectoralesService implements IEstadisticasElectoralesService
{
    public function contarEleccionesActivas(): int
    {
        return Elecciones::where('idEstado', '=', EstadoElecciones::PROGRAMADO)->count();
    }

    public function contarElectoresHabilitadosPorEleccion(Elecciones $eleccion): int
    {
        return PadronElectoral::where('idElecciones', '=', $eleccion->getKey())->count();
    }

    public function contarElectoresHabilitadosPorEleccionYArea(Elecciones $eleccion, Area $area): int
    {
        return PadronElectoral::where('idElecciones', '=', $eleccion->getKey())
            ->join('User', 'PadronElectoral.idUsuario', '=', 'User.idUser')
            ->join('PerfilUsuario', 'User.idUser', '=', 'PerfilUsuario.idUser')
            ->where('PerfilUsuario.idArea', '=', $area->getKey())
            ->count();
    }

    public function contarCantidadVotosPorEleccion(Elecciones $eleccion): int
    {
        return PadronElectoral::where('idElecciones', '=', $eleccion->getKey())
            ->whereNotNull('fechaVoto')
            ->count();
    }

    public function contarCantidadVotosPorEleccionYArea(Elecciones $eleccion, Area $area): int
    {
        return PadronElectoral::where('idElecciones', '=', $eleccion->getKey())
            ->join('User', 'PadronElectoral.idUsuario', '=', 'User.idUser')
            ->join('PerfilUsuario', 'User.idUser', '=', 'PerfilUsuario.idUser')
            ->where('PerfilUsuario.idArea', '=', $area->getKey())
            ->whereNotNull('PadronElectoral.fechaVoto')
            ->count();
    }

    public function calcularPorcentajeParticipacionPorEleccion(Elecciones $eleccion): float
    {
        $totalHabilitados = $this->contarElectoresHabilitadosPorEleccion($eleccion);
        if ($totalHabilitados === 0) return -1;

        $votosEmitidos = $this->contarCantidadVotosPorEleccion($eleccion);

        return ($votosEmitidos / $totalHabilitados) * 100;
    }

    public function calcularPorcentajeParticipacionPorEleccionYArea(Elecciones $eleccion, Area $area): float
    {
        $totalHabilitados = $this->contarElectoresHabilitadosPorEleccionYArea($eleccion, $area);
        if ($totalHabilitados === 0) return -1;

        $votosEmitidos = $this->contarCantidadVotosPorEleccionYArea($eleccion, $area);

        return ($votosEmitidos / $totalHabilitados) * 100;
    }
}
