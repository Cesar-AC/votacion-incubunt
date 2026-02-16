<?php

namespace App\Services;

use App\Enum\Config;
use App\Models\Configuracion;
use App\Models\Elecciones;
use App\Interfaces\Services\IEleccionesService;
use App\Models\User;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use Illuminate\Support\Collection;
use App\Models\Cargo;
use App\Models\EstadoElecciones;
use App\Models\Partido;
use Carbon\Carbon;

class EleccionesService implements IEleccionesService
{
    protected ?Elecciones $eleccionActiva;

    protected function validarEleccionParaServicio(Elecciones $eleccion)
    {
        if (!$eleccion->estaProgramado()) {
            throw new \Exception('No se puede configurar esta elección en el servicio: La elección no está programada.');
        }
    }

    protected function obtenerEleccionOFalla(?Elecciones $eleccion = null): Elecciones
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();
        if (!$eleccion) {
            throw new \Exception('No se ha configurado una elección activa.');
        }
        return $eleccion;
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

    public function obtenerEleccionActiva(): ?Elecciones
    {
        return $this->eleccionActiva;
    }

    public function cambiarEleccionActiva(Elecciones $eleccion): void
    {
        $this->guardarEleccionActiva($eleccion);
    }

    public function esEleccionActiva(Elecciones $eleccion): bool
    {
        return $this->obtenerEleccionActiva()?->getKey() == $eleccion->getKey();
    }

    public function estaEnPadronElectoral(User $usuario, ?Elecciones $eleccion = null): bool
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);
        return $eleccion->usuarios()->where('idUser', '=', $usuario->getKey())->exists();
    }

    public function obtenerCandidatos(?Elecciones $eleccion = null): Collection
    {
        return $this->obtenerCandidatosIndividuales($eleccion);
    }

    public function obtenerCandidatosIndividuales(?Elecciones $eleccion = null): Collection
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);

        return $eleccion->candidatos()->where('idPartido', '=', null)->get();
    }

    public function obtenerCandidatosMiembrosDePartido(?Elecciones $eleccion = null): Collection
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);

        return $eleccion->candidatos()->where('idPartido', '!=', null)->get();
    }

    public function obtenerCandidatosDePartido(Partido $partido, ?Elecciones $eleccion = null): Collection
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);

        return $eleccion->candidatos()->where('idPartido', '=', $partido->getKey())->get();
    }

    public function obtenerCandidatosPorCargo(Cargo $cargo, ?Elecciones $eleccion = null): Collection
    {
        throw new \Exception('Not implemented');
    }

    public function obtenerPartidos(?Elecciones $eleccion = null): Collection
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);
        return $eleccion->partidos()->get();
    }

    public function perteneceCandidatoAEleccion(Candidato $candidato, ?Elecciones $eleccion = null): bool
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);
        return $eleccion->candidatos()->where('Candidato.idCandidato', '=', $candidato->getKey())->exists();
    }

    public function pertenecePartidoAEleccion(Partido $partido, ?Elecciones $eleccion = null): bool
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);
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
    public function editarElecciones(array $datos, ?Elecciones $eleccion = null): Elecciones
    {
        $eleccion = $eleccion ?? $this->obtenerEleccionActiva();

        $eleccion->update([
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'fechaInicio' => $datos['fechaInicio'],
            'fechaCierre' => $datos['fechaCierre'],
        ]);

        if (isset($datos['idEstado']) && $datos['idEstado'] == EstadoElecciones::ANULADO) {
            $this->anularElecciones($eleccion);
        }

        if (isset($datos['idEstado']) && $datos['idEstado'] == EstadoElecciones::FINALIZADO) {
            $this->finalizarElecciones($eleccion);
        }

        return $eleccion;
    }

    public function anularElecciones(?Elecciones $eleccion = null): void
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);
        if (!$eleccion->estaProgramado()) {
            throw new \Exception('No se puede anular una elección que no se encuentra en estado "Programado".');
        }

        if ($this->esEleccionActiva($eleccion)) {
            $this->eleccionActiva = null;
            Configuracion::definirClave(Config::ELECCION_ACTIVA, '-1');
        }

        $eleccion->update([
            'idEstado' => EstadoElecciones::ANULADO
        ]);
    }

    public function finalizarElecciones(?Elecciones $eleccion = null): void
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);
        if (!$eleccion->estaProgramado()) {
            throw new \Exception('No se puede finalizar una elección que no se encuentra en estado "Programado".');
        }

        if ($this->esEleccionActiva($eleccion)) {
            $this->eleccionActiva = null;
            Configuracion::definirClave(Config::ELECCION_ACTIVA, '-1');
        }

        $eleccion->update([
            'idEstado' => EstadoElecciones::FINALIZADO
        ]);
    }

    public function restaurarElecciones(?Elecciones $eleccion = null): void
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);

        if (!$eleccion->estaAnulado()) {
            throw new \Exception('Solo se puede restaurar una elección que se encuentra en estado "Anulada".');
        }

        $eleccion->idEstado = EstadoElecciones::PROGRAMADO;
        $eleccion->save();
    }

    public function votacionHabilitada(?Elecciones $eleccion = null): bool
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);

        $fechaActual = Carbon::now();
        return $fechaActual->between($eleccion->fechaInicio, $eleccion->fechaCierre);
    }

    public function votacionPosteriorAFechaCierre(?Elecciones $eleccion = null): bool
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);

        $fechaActual = Carbon::now();
        return $fechaActual->greaterThanOrEqualTo($eleccion->fechaCierre);
    }

    public function obtenerCandidatoEleccion(Candidato $candidato, ?Elecciones $eleccion = null): CandidatoEleccion
    {
        $eleccion = $this->obtenerEleccionOFalla($eleccion);
        return CandidatoEleccion::where('idCandidato', '=', $candidato->getKey())
            ->where('idElecciones', '=', $eleccion->getKey())
            ->first();
    }
}
