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
use App\Models\EstadoElecciones;
use App\Models\Partido;
use App\Models\VotoCandidato;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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

    public function obtenerCandidatos(?Elecciones $eleccion): Collection
    {
        throw new \Exception('Not implemented');
    }

    public function obtenerCandidatosPorCargo(Cargo $cargo, ?Elecciones $eleccion): Collection
    {
        throw new \Exception('Not implemented');
    }

    public function obtenerPartidos(?Elecciones $eleccion): Collection
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        return $eleccion->partidos()->get();
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

    public function obtenerTodasLasElecciones(): Collection
    {
        return Elecciones::where('idEstado', '!=', EstadoElecciones::ANULADO)->get();
    }

    public function obtenerTodasLasEleccionesAnuladas(): Collection
    {
        return Elecciones::where('idEstado', '=', EstadoElecciones::ANULADO)->get();
    }

    public function obtenerTodasLasEleccionesProgramables(): Collection
    {
        return Elecciones::where('idEstado', '=', EstadoElecciones::PROGRAMADO)->get();
    }

    public function obtenerEleccionPorId(int $id): Elecciones
    {
        return Elecciones::findOrFail($id);
    }

    /**
     * @param array{'titulo': string, 'descripcion': string, 'fechaInicio': Carbon|string, 'fechaCierre': Carbon|string} $datos
     *      Obligatorio.
     *      Los datos de la elección que se desea crear.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function crearElecciones(array $datos): Elecciones
    {
        $datos['idEstado'] = EstadoElecciones::PROGRAMADO;

        $eleccion = Elecciones::create($datos);
        return $eleccion;
    }

    /**
     * @param array{'titulo': string, 'descripcion': string, 'fechaInicio': Carbon|string, 'fechaCierre': Carbon|string} $datos
     *      Obligatorio.
     *      Los datos de la elección que se desea editar.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editarElecciones(array $datos, ?Elecciones $eleccion): Elecciones
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();

        $eleccion->update($datos);

        return $eleccion;
    }

    public function anularElecciones(?Elecciones $eleccion): void
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        if (!$eleccion->estaProgramado()) {
            throw new \Exception('No se puede anular una elección que no se encuentra en estado "Programado".');
        }

        if ($this->esEleccionActiva($eleccion)) {
            $this->eleccionActiva = null;
        }

        $eleccion->update([
            'idEstado' => EstadoElecciones::ANULADO
        ]);
    }

    public function finalizarElecciones(?Elecciones $eleccion): void
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        if (!$eleccion->estaProgramado()) {
            throw new \Exception('No se puede finalizar una elección que no se encuentra en estado "Programado".');
        }

        if ($this->esEleccionActiva($eleccion)) {
            $this->eleccionActiva = null;
        }

        $eleccion->update([
            'idEstado' => EstadoElecciones::FINALIZADO
        ]);
    }

    public function restaurarElecciones(?Elecciones $eleccion): void
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();

        if (!$eleccion->estaAnulado()) {
            throw new \Exception('Solo se puede restaurar una elección que se encuentra en estado "Anulada".');
        }

        $eleccion->idEstado = EstadoElecciones::PROGRAMADO;
        $eleccion->save();
    }

    public function votacionHabilitada(?Elecciones $eleccion): bool
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();

        $fechaActual = Carbon::now();
        return $fechaActual->between($eleccion->fechaInicio, $eleccion->fechaCierre);
    }
}
