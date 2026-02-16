<?php

namespace App\Http\Controllers;

use App\Interfaces\IEstadisticasElectoralesService;
use App\Models\Area;
use App\Models\Elecciones;

class KPIController extends Controller
{
    protected IEstadisticasElectoralesService $estadisticasPadronElectoral;

    public function __construct(IEstadisticasElectoralesService $estadisticasPadronElectoral)
    {
        $this->estadisticasPadronElectoral = $estadisticasPadronElectoral;
    }

    public function obtenerCantidadEleccionesActivas(){
        return [
            'success' => true,
            'message' => 'Cantidad de elecciones activas obtenida con éxito',
            'data' => $this->estadisticasPadronElectoral->contarEleccionesActivas()
        ];
    }

    public function obtenerCantidadElectoresHabilitados(Elecciones $eleccion){
        return [
            'success' => true,
            'message' => 'Cantidad de electores habilitados obtenida con éxito',
            'data' => $this->estadisticasPadronElectoral->contarElectoresHabilitadosPorEleccion($eleccion)
        ];
    }

    public function obtenerCantidadElectoresHabilitadosPorArea(Elecciones $eleccion, Area $area){
        return [
            'success' => true,
            'message' => 'Cantidad de electores habilitados por área obtenida con éxito',
            'data' => $this->estadisticasPadronElectoral->contarElectoresHabilitadosPorEleccionYArea($eleccion, $area),
        ];
    }

    public function obtenerPorcentajeParticipacionPorEleccion(Elecciones $eleccion){
        $participacion = $this->estadisticasPadronElectoral->calcularPorcentajeParticipacionPorEleccion($eleccion);

        if ($participacion === -1) {
            return [
                'success' => true,
                'message' => 'No hay electores habilitados para esta elección',
                'data' => '',
            ];
        };

        return [
            'success' => true,
            'message' => 'Porcentaje de participación por elección obtenido con éxito',
            'data' => $participacion,
        ];
    }

    public function obtenerPorcentajeParticipacionPorArea(Elecciones $eleccion, Area $area){
        $participacion = $this->estadisticasPadronElectoral->calcularPorcentajeParticipacionPorEleccionYArea($eleccion, $area);

        if ($participacion === -1) {
            return [
                'success' => true,
                'message' => 'No hay electores habilitados para esta elección',
                'data' => '',
            ];
        };

        return [
            'success' => true,
            'message' => 'Porcentaje de participación por área obtenido con éxito',
            'data' => $participacion,
        ];
    }
}
