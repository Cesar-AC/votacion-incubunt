<?php

namespace App\Interfaces\Services;

use App\Interfaces\DTO\Services\IVotosCandidatoDTO;
use App\Interfaces\DTO\Services\IVotosPartidoDTO;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Elecciones;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Devuelve información sobre las elecciones y sus candidatos.
 * @package App\Interfaces\Services
 */
interface IEleccionesService
{
    /**
     * @return Elecciones Retorna la elección activa.
     *      Si no se ha configurado una elección activa, retornará null.
     */
    public function obtenerEleccionActiva(): ?Elecciones;

    /**
     * @param Elecciones $eleccion
     *      Obligatorio.
     *      La elección que se desea marcar como activa.
     * @return void
     * @throws \Exception Solo si la elección está marcada como anulada o finalizada.
     */
    public function cambiarEleccionActiva(Elecciones $eleccion): void;

    /**
     * @param Elecciones $eleccion
     *      Obligatorio.
     *      La elección que se desea comprobar si es la activa.
     * @return bool Retorna true si la elección es la activa, false en caso contrario.
     */
    public function esEleccionActiva(Elecciones $eleccion): bool;

    /**
     * @param User $usuario
     *      Obligatorio.
     *      El usuario que se desea comprobar si pertenece a una elección.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return bool Retorna true si el usuario pertenece a la elección, false en caso contrario.
     */
    public function estaEnPadronElectoral(User $usuario, ?Elecciones $eleccion): bool;

    /**
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return Collection<Candidato>
     *      Retorna una colección de candidatos.
     */
    public function obtenerCandidatos(?Elecciones $eleccion): Collection;

    /**
     * @param Cargo $cargo
     *      Obligatorio.
     *      Los candidatos se obtendrán en base al cargo especificado.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return Collection<Candidato>
     *      Retorna una colección de candidatos.
     */
    public function obtenerCandidatosPorCargo(Cargo $cargo, ?Elecciones $eleccion): Collection;

    /**
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return Collection<Partido>
     *      Retorna una colección de partidos.
     */
    public function obtenerPartidos(?Elecciones $eleccion): Collection;

    /**
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea comprobar si pertenece a una elección.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return bool Retorna true si el candidato pertenece a la elección, false en caso contrario.
     */
    public function perteneceCandidatoAEleccion(Candidato $candidato, ?Elecciones $eleccion): bool;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido que se desea comprobar si pertenece a una elección.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return bool Retorna true si el partido pertenece a la elección, false en caso contrario.
     */
    public function pertenecePartidoAEleccion(Partido $partido, ?Elecciones $eleccion): bool;

    /**
     * @return Collection<Elecciones>
     *      Retorna la lista de elecciones.
     *      Devolverá incluso las elecciones anuladas. 
     *      En caso de que no se desee, se debe utilizar el método obtenerTodasLasEleccionesProgramables().
     */
    public function obtenerTodasLasElecciones(): Collection;

    /**
     * @return Collection<Elecciones>
     *      Retorna la lista de elecciones anuladas.
     */
    public function obtenerTodasLasEleccionesAnuladas(): Collection;

    /**
     * @return Collection<Elecciones>
     *      Retorna la lista de elecciones programables.
     */
    public function obtenerTodasLasEleccionesProgramables(): Collection;

    /**
     * @param int $id
     *      Obligatorio.
     *      El id de la elección que se desea obtener.
     * @return Elecciones
     *      Retorna la elección con el id especificado.
     * @throws \Exception Si no se encuentra la elección.
     */
    public function obtenerEleccionPorId(int $id): Elecciones;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos de la elección que se desea crear.
     * @return Elecciones
     *      Retorna la elección creada.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function crearElecciones(array $datos): Elecciones;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los nuevos datos que se desea asignar a la elección.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return Elecciones
     *      Retorna la elección editada.
     * @throws \Exception Solo si la elección no se encuentra en estado "Programada".
     */
    public function editarElecciones(array $datos, ?Elecciones $eleccion): Elecciones;

    /**
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return void
     * @throws \Exception Solo si la elección no se encuentra en estado "Programada".
     */
    public function anularElecciones(?Elecciones $eleccion): void;

    /**
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return void
     * @throws \Exception Solo si la elección no se encuentra en estado "Programada".
     */
    public function finalizarElecciones(?Elecciones $eleccion): void;

    /**
     * @param Elecciones $eleccion
     *      Obligatorio.
     *      La elección que se desea restaurar.
     * @return void
     * @throws \Exception Solo si la elección no se encuentra en estado "Anulada".
     */
    public function restaurarElecciones(Elecciones $eleccion): void;

    /**
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return bool
     *      Retorna true si la votación está habilitada, false en caso contrario.
     */
    public function votacionHabilitada(?Elecciones $eleccion): bool;
}
