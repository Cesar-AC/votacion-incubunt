<?php

namespace App\Http\Controllers;

use App\Interfaces\IEstadisticasElectoralesService;
use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\ICandidatoService;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use App\Interfaces\Services\IVotoService;
use App\Models\Area;

use Barryvdh\DomPDF\Facade\Pdf;

class VotoController extends Controller
{
    public function __construct(
        protected IVotoService $votoService,
        protected IEleccionesService $eleccionesService,
        protected IAreaService $areaService,
        protected IPartidoService $partidoService,
        protected ICandidatoService $candidatoService,
        protected IEstadisticasElectoralesService $eeService
    ) {}

    public function index()
    {
        $elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();
        $areas = $this->areaService->obtenerAreas();
        $presidencia = $this->areaService->obtenerAreaPorId(Area::PRESIDENCIA);

        return view('crud.voto.ver', array_merge(
            compact('elecciones', 'areas', 'presidencia'),
            [
                'partidoService' => $this->partidoService,
                'eeService' => $this->eeService,
                'candidatoService' => $this->candidatoService,
                'mostrarBotonExportar' => true,
            ]
        ));
    }

    public function verResultadoElecciones($idEleccion)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($idEleccion);

        if (!$eleccion->estaFinalizado()) {
            return back()->withErrors('No se puede ver los resultados de una elección que no ha finalizado.');
        }

        $elecciones = [$eleccion];
        $areas = $this->areaService->obtenerAreas();
        $presidencia = $this->areaService->obtenerAreaPorId(Area::PRESIDENCIA);

        return view('crud.voto.ver', array_merge(
            compact('elecciones', 'areas', 'presidencia'),
            [
                'partidoService' => $this->partidoService,
                'eeService' => $this->eeService,
                'candidatoService' => $this->candidatoService,
                'mostrarBotonExportar' => true,
            ]
        ));
    }

    public function generarPDF($idEleccion)
    {
        $eleccion = $this->eleccionesService->obtenerEleccionPorId($idEleccion);
        $areas = $this->areaService->obtenerAreas();
        $presidencia = $this->areaService->obtenerAreaPorId(Area::PRESIDENCIA);

        $pdf = Pdf::loadView('crud.voto.pdf', array_merge(
            compact('eleccion', 'areas', 'presidencia'),
            [
                'partidoService' => $this->partidoService,
                'eeService' => $this->eeService,
                'candidatoService' => $this->candidatoService,
            ]
        ));

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('chroot', public_path());
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream('reporte_eleccion_' . $idEleccion . '.pdf');
    }
}
