<?php

namespace App\Interfaces\Services;

use App\Interfaces\DTO\Services\IVotosCandidatoDTO;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Elecciones;
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
     */
    public function obtenerEleccionActiva(): Elecciones;

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
    public function obtenerCandidatos(Cargo $cargo, ?Elecciones $eleccion): Collection;

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
     *      Opcional.
     *      Si es enviado, el método devuelve la cantidad de votos del candidato en la elección especificado (o la por defecto).
     *      Si no, el método devuelve la cantidad de votos de todos los candidatos en la elección especificada.
     * @param Elecciones $eleccion
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return Collection<IVotosCandidatoDTO>|IVotosCandidatoDTO
     *      Retorna un objeto IVotosCandidatoDTO de un solo candidato si se especificó uno.
     *      Caso contrario, retorna una colección conteniendo un objeto IVotosCandidatoDTO para cada candidato.
     */
    public function obtenerVotos(?Candidato $candidato, ?Elecciones $eleccion): Collection|IVotosCandidatoDTO;

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
}
