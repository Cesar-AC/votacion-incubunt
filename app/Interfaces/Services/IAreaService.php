<?php

namespace App\Interfaces\Services;

use App\Models\Area;
use Illuminate\Support\Collection;

interface IAreaService
{
    /**
     * @return Collection<Area>
     *      Retorna la lista de áreas, excluyendo la presidencia y el área sin asignar.
     */
    public function obtenerAreas(): Collection;

    /**
     * @return Collection<Area>
     *      Retorna la lista de áreas, sin excluir la presidencia y el área sin asignar.
     */
    public function obtenerTodasLasAreas(): Collection;

    /**
     * @param int $id
     *      Obligatorio.
     *      El id de la área que se desea obtener.
     * @return Area
     *      Retorna la área con el id especificado.
     * @throws \Exception Si no se encuentra la área.
     */
    public function obtenerAreaPorId(int $id): Area;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos de la área que se desea crear.
     * @return Area
     *      Retorna la área creada.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function crearArea(array $datos): Area;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los nuevos datos que se desea asignar a la área.
     * @param Area $area
     *      Obligatorio.
     *      La área que se desea editar.
     * @return Area
     *      Retorna la área editada.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function editarArea(array $datos, Area $area): Area;

    /**
     * @param Area $area
     *      Obligatorio.
     *      La área que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envía la área.
     */
    public function eliminarArea(Area $area): void;
}
