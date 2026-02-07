<?php

namespace App\Interfaces\Services;

use App\Models\Carrera;
use Illuminate\Support\Collection;

interface ICarreraService
{
    /**
     * @return Collection<Carrera>
     *      Retorna la lista de carreras.
     */
    public function obtenerCarreras(): Collection;

    /**
     * @param int $id
     *      Obligatorio.
     *      El id de la carrera que se desea obtener.
     * @return Carrera
     *      Retorna la carrera con el id especificado.
     * @throws \Exception Si no se encuentra la carrera.
     */
    public function obtenerCarreraPorId(int $id): Carrera;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos de la carrera que se desea crear.
     * @return Carrera
     *      Retorna la carrera creada.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function crearCarrera(array $datos): Carrera;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los nuevos datos que se desea asignar a la carrera.
     * @param Carrera $carrera
     *      Obligatorio.
     *      La carrera que se desea editar.
     * @return Carrera
     *      Retorna la carrera editada.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function editarCarrera(array $datos, Carrera $carrera): Carrera;

    /**
     * @param Carrera $carrera
     *      Obligatorio.
     *      La carrera que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envía la carrera.
     */
    public function eliminarCarrera(Carrera $carrera): void;
}
