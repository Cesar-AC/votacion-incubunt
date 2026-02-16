<?php

namespace App\Interfaces\Services;

use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\PropuestaPartido;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface IPartidoService
{
    /**
     * @return Collection<Partido>
     *      Retorna la lista de partidos.
     */
    public function obtenerPartidos(): Collection;

    /**
     * @param ?Elecciones $elecciones
     *      Opcional.
     *      Si es enviado, el método utilizará la elección especificada.
     *      Si no, el método utilizará la elección activa.
     * @return Collection<Partido>
     *      Retorna la lista de partidos de la elección.
     */
    public function obtenerPartidosInscritosEnEleccion(?Elecciones $elecciones): Collection;

    /**
     * @param int $id
     *      Obligatorio.
     *      El id del partido que se desea obtener.
     * @return Partido
     *      Retorna el partido con el id especificado.
     * @throws \Exception Si no se encuentra el partido.
     */
    public function obtenerPartidoPorId(int $id): Partido;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos del partido que se desea crear.
     * @return Partido
     *      Retorna el partido creado.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function crearPartido(array $datos): Partido;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los nuevos datos que se desea asignar al partido.
     * @param Partido $partido
     *      Obligatorio.
     *      El partido que se desea editar.
     * @return Partido
     *      Retorna el partido editado.
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function editarPartido(array $datos, Partido $partido): Partido;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envía el partido.
     */
    public function eliminarPartido(Partido $partido): void;

    /**
     * @param int $idPropuestaPartido
     *      Obligatorio.
     *      El id de la propuesta que se desea obtener.
     * @return PropuestaPartido
     *      Retorna la propuesta con el id especificado.
     * @throws \Exception Si no se encuentra la propuesta.
     */
    public function obtenerPropuestaDePartido(int $idPropuestaPartido): PropuestaPartido;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido que se desea obtener.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea obtener el partido.
     * @return Collection<PropuestaPartido>
     *      Retorna la lista de propuestas del partido.
     * @throws \Exception Si no se encuentra el partido.
     */
    public function obtenerPropuestasDePartidoEnElecciones(Partido $partido, Elecciones $elecciones): Collection;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos de la propuesta que se desea añadir.
     * @param Partido $partido
     *      Obligatorio.
     *      El partido al que se desea añadir la propuesta.
     * @param Elecciones $elecciones
     *      Obligatorio.
     *      La elección en la que se desea añadir la propuesta.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function añadirPropuestaDePartido(array $datos, Partido $partido, Elecciones $elecciones): void;

    /**
     * @param array $datos
     *      Obligatorio.
     *      Los datos de la propuesta que se desea actualizar.
     * @param PropuestaPartido $propuestaPartido
     *      Obligatorio.
     *      La propuesta que se desea actualizar.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function actualizarPropuestaDePartido(array $datos, PropuestaPartido $propuestaPartido): void;

    /**
     * @param PropuestaPartido $propuestaPartido
     *      Obligatorio.
     *      La propuesta que se desea eliminar.
     * @return void
     * @throws \Exception Si no se envían los datos necesarios.
     */
    public function eliminarPropuestaDePartido(PropuestaPartido $propuestaPartido): void;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido que se desea inscribir.
     * @param ?Elecciones $elecciones
     *      Opcional.
     *      La elección en la que se desea inscribir el partido.
     *      Si no se envía, se utilizará la elección activa.
     * @return void
     * @throws \Exception Si la elección no está programada.
     */
    public function inscribirPartidoEnElecciones(Partido $partido, ?Elecciones $elecciones = null): void;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido que se desea remover.
     * @param ?Elecciones $elecciones
     *      Opcional.
     *      La elección en la que se desea remover el partido.
     *      Si no se envía, se utilizará la elección activa.
     * @return void
     * @throws \Exception Si la elección no está programada.
     */
    public function removerPartidoDeElecciones(Partido $partido, ?Elecciones $elecciones): void;

    /**
     * Agrega al partido a las elecciones especificadas. Elimina al partido de las elecciones no especificadas que sean programables.
     * Solo puede actualizar elecciones programadas.
     * 
     * @param Partido $partido
     *      Obligatorio.
     *      El partido que participará de las elecciones establecidas.
     * @param Collection<Elecciones> $elecciones
     *      Obligatorio.
     *      Las elecciones a las que se desea que pertenezca el partido.
     * @return void
     * @throws \Exception Si la elección no está programada.
     */
    public function establecerEleccionesDePartido(Partido $partido, Collection $elecciones): void;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido al que se desea subir la foto.
     * @param UploadedFile $archivo
     *      Obligatorio.
     *      El archivo que se desea subir.
     * @return void
     * @throws \Exception Si el partido ya tiene una foto.
     * @see self::cambiarFoto() En caso de no saber si el partido tiene foto, es mejor utilizarlo.
     */
    public function subirFoto(Partido $partido, UploadedFile $archivo): void;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido al que se desea remover la foto.
     * @return void
     * @throws \Exception Si el partido no tiene foto.
     * @see self::cambiarFoto() En caso de no saber si el partido tiene foto, es mejor utilizarlo.
     */
    public function removerFoto(Partido $partido): void;

    /**
     * Cambia la foto del partido, exista o no exista.
     * 
     * @param Partido $partido
     *      Obligatorio.
     *      El partido al que se desea cambiar la foto.
     * @param UploadedFile $archivo
     *      Obligatorio.
     *      El archivo que se desea asignar como foto.
     * @return void
     */
    public function cambiarFoto(Partido $partido, UploadedFile $archivo): void;

    /**
     * @param Partido $partido
     *      Obligatorio.
     *      El partido al que se desea obtener la foto.
     * @return string|null
     *      Retorna la URL pública de la foto del partido.
     *      Retorna null si el partido no tiene foto.
     */
    public function obtenerFotoURL(Partido $partido): ?string;
}
