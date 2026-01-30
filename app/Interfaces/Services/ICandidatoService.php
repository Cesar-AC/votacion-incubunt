<?php

namespace App\Interfaces\Services;

use App\Models\Candidato;
use App\Models\Elecciones;
use App\Models\PropuestaCandidato;
use Illuminate\Support\Collection;

interface ICandidatoService
{
    /**
     * @return Collection<Candidato>
     *      Retorna la lista de candidatos.
     */
    public function obtenerCandidatos(): Collection;

    /**
     * @param int $id
     *      Obligatorio.
     *      El id del candidato que se desea obtener.
     * @return Candidato
     *      Retorna el candidato con el id especificado.
     * @throws \Exception Si no se encuentra el candidato.
     */
    public function obtenerCandidatoPorId(int $id): Candidato;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos del candidato que se desea crear.
     * @return Candidato
     *      Retorna el candidato creado.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function crearCandidato(array $datos): Candidato;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los nuevos datos que se desea asignar al candidato.
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea editar.
     * @return Candidato
     *      Retorna el candidato editado.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function editarCandidato(array $datos, Candidato $candidato): Candidato;

    /**
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envía el candidato.
     */
    public function eliminarCandidato(Candidato $candidato): void;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos del candidato que se desea vincular.
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea vincular.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea vincular el candidato al partido.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function actualizarPartidoDeCandidatoEnElecciones(array $datos, Candidato $candidato, Elecciones $elecciones): void;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos del candidato que se desea vincular.
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea vincular.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea vincular el candidato al cargo.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function actualizarCargoDeCandidatoEnElecciones(array $datos, Candidato $candidato, Elecciones $elecciones): void;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos del candidato que se desea vincular.
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea vincular.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea vincular el candidato.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function vincularCandidatoAEleccion(array $datos, Candidato $candidato, Elecciones $elecciones): void;

    /**
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea desvincular.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea desvincular el candidato.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function desvincularCandidatoDeEleccion(Candidato $candidato, Elecciones $elecciones): void;

    /**
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea remover.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea remover el candidato.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function removerPartidoDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): void;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos del candidato que se desea actualizar.
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea actualizar.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea actualizar el candidato.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function actualizarDatosDeCandidatoEnElecciones(array $datos, Candidato $candidato, Elecciones $elecciones): void;

    /**
     * @param int $idPropuestaCandidato
     *      Obligatorio.
     *      El id de la propuesta que se desea obtener.
     * @return PropuestaCandidato
     *      Retorna la propuesta con el id especificado.
     * @throws \Exception Si no se encuentra la propuesta.
     */
    public function obtenerPropuestaDeCandidato(int $idPropuestaCandidato): PropuestaCandidato;

    /**
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato que se desea obtener.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea obtener el candidato.
     * @return Collection<PropuestaCandidato>
     *      Retorna la lista de propuestas del candidato.
     * @throws \Exception Si no se encuentra el candidato.
     */
    public function obtenerPropuestasDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): Collection;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos de la propuesta que se desea añadir.
     * @param Candidato $candidato
     *      Obligatorio.
     *      El candidato al que se desea añadir la propuesta.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea añadir la propuesta.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function añadirPropuestaDeCandidato(array $datos, Candidato $candidato, Elecciones $elecciones): void;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos de la propuesta que se desea actualizar.
     * @param PropuestaCandidato $propuestaCandidato
     *      Obligatorio.
     *      La propuesta que se desea actualizar.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function actualizarPropuestaDeCandidato(array $datos, PropuestaCandidato $propuestaCandidato): void;

    /**
     * @param int $idPropuestaCandidato
     *      Obligatorio.
     *      El id de la propuesta que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function eliminarPropuestaDeCandidato(int $idPropuestaCandidato): void;
}
