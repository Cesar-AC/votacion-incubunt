<?php

namespace App\Services;

use App\DTO\Services\VotosCandidatoDTO;
use App\Enum\Config;
use App\Interfaces\DTO\Services\IVotosCandidatoDTO;
use App\Models\Configuracion;
use App\Models\Elecciones;
use App\Interfaces\Services\IEleccionesService;
use App\Models\User;
use App\Models\Candidato;
use Illuminate\Support\Collection;
use App\Models\Cargo;
use App\Models\Partido;

class EleccionesService implements IEleccionesService
{
    protected ?Elecciones $eleccionActiva;

    protected function validarEleccionParaServicio(Elecciones $eleccion)
    {
        if (!$eleccion->estaProgramado()) {
            throw new \Exception('No se puede configurar esta elección en el servicio: La elección no está programada.');
        }
    }

    protected function guardarEleccionActiva(Elecciones $eleccion): void
    {
        $this->validarEleccionParaServicio($eleccion);
        $this->eleccionActiva = $eleccion;
        Configuracion::definirClave(Config::ELECCION_ACTIVA, $eleccion->getKey());
    }

    public function __construct(?Elecciones $eleccionActiva)
    {
        // Si no hay elección configurada aún, permitimos null y se lanzará
        // excepción solo cuando se solicite la activa.
        if ($eleccionActiva !== null) {
            $this->guardarEleccionActiva($eleccionActiva);
        } else {
            $this->eleccionActiva = null;
        }
    }

    public function obtenerEleccionActiva(): Elecciones
    {
        if ($this->eleccionActiva === null) {
            throw new \Exception('No se ha configurado una elección activa.');
        }

        return $this->eleccionActiva;
    }

    public function cambiarEleccionActiva(Elecciones $eleccion): void
    {
        $this->guardarEleccionActiva($eleccion);
    }

    public function esEleccionActiva(Elecciones $eleccion): bool
    {
        return $this->obtenerEleccionActiva()->getKey() == $eleccion->getKey();
    }

    public function estaEnPadronElectoral(User $usuario, ?Elecciones $eleccion): bool
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        return $eleccion->usuarios()->where('idUser', '=', $usuario->getKey())->exists();
    }

    public function obtenerCandidatos(Cargo $cargo, ?Elecciones $eleccion): Collection
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        return $eleccion->candidatos()
            ->where('idCargo', '=', $cargo->getKey())
            ->get();
    }

    public function obtenerPartidos(?Elecciones $eleccion): Collection
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        return $eleccion->partidos()->get();
    }

    public function obtenerVotos(?Candidato $candidato, ?Elecciones $eleccion): Collection|IVotosCandidatoDTO
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        $candidato = $candidato ?? $eleccion->candidatos;

        if ($candidato instanceof Collection) {
            return $candidato->map(function (Candidato $candidato) use ($eleccion) {
                return new VotosCandidatoDTO(
                    $candidato,
                    $candidato->votos()->where('idElecciones', '=', $eleccion->getKey())->count()
                );
            });
        }

        return new VotosCandidatoDTO(
            $candidato,
            $candidato->votos()->where('idElecciones', '=', $eleccion->getKey())->count()
        );
    }

    public function perteneceCandidatoAEleccion(Candidato $candidato, ?Elecciones $eleccion): bool
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        return $eleccion->candidatos()->where('Candidato.idCandidato', '=', $candidato->getKey())->exists();
    }

    public function pertenecePartidoAEleccion(Partido $partido, ?Elecciones $eleccion): bool
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        return $eleccion->partidos()->where('Partido.idPartido', '=', $partido->getKey())->exists();
    }
}
