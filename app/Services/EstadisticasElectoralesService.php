<?php

namespace App\Services;

use App\Interfaces\IEstadisticasElectoralesService;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\PadronElectoral;
use App\Models\Area;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Partido;
use App\Models\TipoVoto;
use App\Models\VotoCandidato;
use App\Models\VotoPartido;

class EstadisticasElectoralesService implements IEstadisticasElectoralesService
{
    public function __construct(
        protected AreaService $areaService,
        protected EleccionesService $eleccionesService,
        protected CargoService $cargoService,
    ) {}

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

    public function contarCantidadVotosMismaAreaParaCandidato(Elecciones $eleccion, Candidato $candidato): int
    {
        return VotoCandidato::where('idElecciones', '=', $eleccion->getKey())
            ->where('idCandidato', '=', $candidato->getKey())
            ->where('idTipoVoto', '=', TipoVoto::ID_MISMA_AREA)
            ->count();
    }

    public function contarCantidadVotosAreaExternaParaCandidato(Elecciones $eleccion, Candidato $candidato): int
    {
        return VotoCandidato::where('idElecciones', '=', $eleccion->getKey())
            ->where('idCandidato', '=', $candidato->getKey())
            ->where('idTipoVoto', '=', TipoVoto::ID_OTRA_AREA)
            ->count();
    }

    protected function ponderarVotos(int $cantidadMismaArea, int $cantidadExterna): int
    {
        return $cantidadMismaArea * TipoVoto::mismaArea()->peso + $cantidadExterna * TipoVoto::otraArea()->peso;
    }

    public function contarCantidadVotosPonderadosParaCandidato(Elecciones $eleccion, Candidato $candidato): int
    {
        $cantidadMismaArea = $this->contarCantidadVotosMismaAreaParaCandidato($eleccion, $candidato);
        $cantidadExterna = $this->contarCantidadVotosAreaExternaParaCandidato($eleccion, $candidato);

        return $this->ponderarVotos($cantidadMismaArea, $cantidadExterna);
    }

    public function calcularPorcentajeVotosMismaAreaParaCandidato(Elecciones $eleccion, Candidato $candidato): float
    {
        $areaPostulacion = $this->areaService->obtenerAreaDePostulacionDeCandidato($candidato, $eleccion);
        $totalHabilitados = $this->contarElectoresHabilitadosPorEleccionYArea($eleccion, $areaPostulacion);

        if ($totalHabilitados === 0) return -1;

        $votosEmitidos = $this->contarCantidadVotosMismaAreaParaCandidato($eleccion, $candidato);

        return ($votosEmitidos / $totalHabilitados) * 100;
    }

    public function calcularPorcentajeVotosAreaExternaParaCandidato(Elecciones $eleccion, Candidato $candidato): float
    {
        $totalHabilitados = $this->contarElectoresHabilitadosPorEleccion($eleccion);

        $areaPostulacion = $this->areaService->obtenerAreaDePostulacionDeCandidato($candidato, $eleccion);
        $areaHabilitados = $this->contarElectoresHabilitadosPorEleccionYArea($eleccion, $areaPostulacion);

        $externosHabilitados = $totalHabilitados - $areaHabilitados;

        if ($externosHabilitados === 0) return -1;

        $votosEmitidos = $this->contarCantidadVotosAreaExternaParaCandidato($eleccion, $candidato);

        return ($votosEmitidos / $externosHabilitados) * 100;
    }

    public function calcularPorcentajeVotosTotalesParaCandidato(Elecciones $eleccion, Candidato $candidato): float
    {
        $totalHabilitados = $this->contarElectoresHabilitadosPorEleccion($eleccion);
        if ($totalHabilitados === 0) return -1;

        $votosMismaArea = $this->contarCantidadVotosMismaAreaParaCandidato($eleccion, $candidato);
        $votosExterna = $this->contarCantidadVotosAreaExternaParaCandidato($eleccion, $candidato);

        $votosEmitidos = $votosMismaArea + $votosExterna;

        return ($votosEmitidos / $totalHabilitados) * 100;
    }

    public function contarCantidadVotosMismaAreaEmitidosParaCargo(Elecciones $eleccion, Cargo $cargo): int
    {
        if ($cargo->area->esPresidencia()) throw new \Exception('Presidencia no distingue tipos de votos');

        return VotoCandidato::where('idElecciones', '=', $eleccion->getKey())
            ->where('idTipoVoto', '=', TipoVoto::ID_MISMA_AREA)
            ->whereHas('candidato.candidatoElecciones', function ($query) use ($cargo, $eleccion) {
                $query->where('CandidatoEleccion.idElecciones', '=', $eleccion->getKey())
                    ->where('CandidatoEleccion.idCargo', '=', $cargo->getKey());
            })
            ->count();
    }

    public function contarCantidadVotosAreaExternaEmitidosParaCargo(Elecciones $eleccion, Cargo $cargo): int
    {
        if ($cargo->area->esPresidencia()) throw new \Exception('Presidencia no distingue tipos de votos');

        return VotoCandidato::where('idElecciones', '=', $eleccion->getKey())
            ->where('idTipoVoto', '=', TipoVoto::ID_OTRA_AREA)
            ->whereHas('candidato.candidatoElecciones', function ($query) use ($cargo, $eleccion) {
                $query->where('CandidatoEleccion.idElecciones', '=', $eleccion->getKey())
                    ->where('CandidatoEleccion.idCargo', '=', $cargo->getKey());
            })
            ->count();
    }

    public function calcularPorcentajeVotosEmitidosParaCandidatoEnCargo(Elecciones $eleccion, Candidato $candidato): float
    {
        $cargo = $this->cargoService->obtenerCargoDePostulacionDeCandidatoEnElecciones($candidato, $eleccion);
        $mismaAreaEmitidosParaCargo = $this->contarCantidadVotosMismaAreaEmitidosParaCargo($eleccion, $cargo);
        $externaAreaEmitidosParaCargo = $this->contarCantidadVotosAreaExternaEmitidosParaCargo($eleccion, $cargo);

        $votosTotalesEmitidosParaCargo = $mismaAreaEmitidosParaCargo + $externaAreaEmitidosParaCargo;

        if ($votosTotalesEmitidosParaCargo === 0) return -1;

        $mismaAreaEmitidosParaCandidato = $this->contarCantidadVotosMismaAreaParaCandidato($eleccion, $candidato);
        $externaAreaEmitidosParaCandidato = $this->contarCantidadVotosAreaExternaParaCandidato($eleccion, $candidato);
        $totalVotosParaCandidato = $mismaAreaEmitidosParaCandidato + $externaAreaEmitidosParaCandidato;

        return ($totalVotosParaCandidato / $votosTotalesEmitidosParaCargo) * 100;
    }

    public function contarCantidadVotosPonderadosParaCargo(Elecciones $eleccion, Cargo $cargo): int
    {
        $mismaAreaEmitidosParaCargo = $this->contarCantidadVotosMismaAreaEmitidosParaCargo($eleccion, $cargo);
        $externaAreaEmitidosParaCargo = $this->contarCantidadVotosAreaExternaEmitidosParaCargo($eleccion, $cargo);

        return $this->ponderarVotos($mismaAreaEmitidosParaCargo, $externaAreaEmitidosParaCargo);
    }

    public function calcularPorcentajeVotosPonderadosParaCandidatoEnCargo(Elecciones $eleccion, Candidato $candidato): float
    {
        $cargo = $this->cargoService->obtenerCargoDePostulacionDeCandidatoEnElecciones($candidato, $eleccion);
        $votosPonderadosParaCargo = $this->contarCantidadVotosPonderadosParaCargo($eleccion, $cargo);

        if ($votosPonderadosParaCargo === 0) return -1;

        $votosPonderadosParaCandidato = $this->contarCantidadVotosPonderadosParaCandidato($eleccion, $candidato);

        return ($votosPonderadosParaCandidato / $votosPonderadosParaCargo) * 100;
    }

    public function contarCantidadVotosParaPartidoEnEleccion(Elecciones $eleccion, Partido $partido): int
    {
        return VotoPartido::where('idElecciones', '=', $eleccion->getKey())
            ->where('idPartido', '=', $partido->getKey())
            ->count();
    }

    public function contarCantidadVotosEmitidosEnPartidosParaEleccion(Elecciones $eleccion): int
    {
        return VotoPartido::where('idElecciones', '=', $eleccion->getKey())
            ->count();
    }

    public function calcularPorcentajeVotosParaPartidoEnEleccion(Elecciones $eleccion, Partido $partido): float
    {
        $totalVotosEmitidos = $this->contarCantidadVotosEmitidosEnPartidosParaEleccion($eleccion);
        if ($totalVotosEmitidos === 0) return -1;

        $votosEmitidos = $this->contarCantidadVotosParaPartidoEnEleccion($eleccion, $partido);

        return ($votosEmitidos / $totalVotosEmitidos) * 100;
    }
}
