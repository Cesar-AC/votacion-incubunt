<?php

namespace App\Interfaces;

use App\Models\Elecciones;
use App\Models\Area;

interface IEstadisticasElectoralesService {
    public function contarEleccionesActivas(): int;
    public function contarElectoresHabilitadosPorEleccion(Elecciones $eleccion): int;
    public function contarElectoresHabilitadosPorEleccionYArea(Elecciones $eleccion, Area $area): int;
    public function contarCantidadVotosPorEleccion(Elecciones $eleccion): int;
    public function contarCantidadVotosPorEleccionYArea(Elecciones $eleccion, Area $area): int;
    public function calcularPorcentajeParticipacionPorEleccion(Elecciones $eleccion): float;
    public function calcularPorcentajeParticipacionPorEleccionYArea(Elecciones $eleccion, Area $area): float;
}