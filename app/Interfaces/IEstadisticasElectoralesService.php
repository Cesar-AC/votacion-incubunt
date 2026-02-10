<?php

namespace App\Interfaces;

use App\Models\Elecciones;
use App\Models\Area;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Partido;

interface IEstadisticasElectoralesService
{
    public function contarEleccionesActivas(): int;
    public function contarElectoresHabilitadosPorEleccion(Elecciones $eleccion): int;
    public function contarElectoresHabilitadosPorEleccionYArea(Elecciones $eleccion, Area $area): int;
    public function contarCantidadVotosPorEleccion(Elecciones $eleccion): int;
    public function contarCantidadVotosPorEleccionYArea(Elecciones $eleccion, Area $area): int;

    public function calcularPorcentajeParticipacionPorEleccion(Elecciones $eleccion): float;
    public function calcularPorcentajeParticipacionPorEleccionYArea(Elecciones $eleccion, Area $area): float;

    public function contarCantidadVotosMismaAreaParaCandidato(Elecciones $eleccion, Candidato $candidato): int;
    public function contarCantidadVotosAreaExternaParaCandidato(Elecciones $eleccion, Candidato $candidato): int;
    public function contarCantidadVotosPonderadosParaCandidato(Elecciones $eleccion, Candidato $candidato): int;

    public function calcularPorcentajeVotosMismaAreaParaCandidato(Elecciones $eleccion, Candidato $candidato): float;
    public function calcularPorcentajeVotosAreaExternaParaCandidato(Elecciones $eleccion, Candidato $candidato): float;
    public function calcularPorcentajeVotosTotalesParaCandidato(Elecciones $eleccion, Candidato $candidato): float;

    public function contarCantidadVotosMismaAreaEmitidosParaCargo(Elecciones $eleccion, Cargo $cargo): int;
    public function contarCantidadVotosAreaExternaEmitidosParaCargo(Elecciones $eleccion, Cargo $cargo): int;
    public function contarCantidadVotosPonderadosParaCargo(Elecciones $eleccion, Cargo $cargo): int;
    public function calcularPorcentajeVotosEmitidosParaCandidatoEnCargo(Elecciones $eleccion, Candidato $candidato): float;
    public function calcularPorcentajeVotosPonderadosParaCandidatoEnCargo(Elecciones $eleccion, Candidato $candidato): float;

    public function contarCantidadVotosParaPartidoEnEleccion(Elecciones $eleccion, Partido $partido): int;
    public function calcularPorcentajeVotosParaPartidoEnEleccion(Elecciones $eleccion, Partido $partido): float;
    public function contarCantidadVotosEmitidosEnPartidosParaEleccion(Elecciones $eleccion): int;
}
