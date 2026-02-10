<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Elección</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #333; }
        .meta-info { font-size: 12px; color: #666; margin-top: 5px; }
        
        /* Basic styles for tables and layout since PDF generators might not support full CSS frameworks */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }
        .mb-3 { margin-bottom: 1rem; }
        .card { border: 1px solid #ddd; margin-bottom: 1rem; }
        .card-header { background-color: #f8f9fa; padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold; }
        .card-body { padding: 10px; }
        .badge { display: inline-block; padding: 0.25em 0.4em; font-size: 75%; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; }
        .badge-success { color: #fff; background-color: #28a745; }
        .badge-secondary { color: #fff; background-color: #6c757d; }

        .img-candidato {
            height: 48px;
        }

        .img-partido {
            height: 120px;
        }

        #titulo-eleccion {
            color: #4e73df;
            font-weight: 800;
            font-size: 1.25rem;
            padding: 0.5rem 0rem;
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>VotaIncubi</h1>
        <div class="meta-info">
            Reporte generado por {{ auth()->user()->perfil?->obtenerNombreApellido() ?? auth()->user()->correo }} en fecha {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary" id="titulo-eleccion">{{ $eleccion->titulo }}</h6>            
            <span class="badge badge-primary"></span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <h2 class="font-bold text-xl my-2">{{$presidencia->area}}</h2>

                <div class="my-2">
                    <p>Electores habilitados: {{ $eeService->contarElectoresHabilitadosPorEleccion($eleccion) }}</p>
                    <p>Electores que han votado: {{ $eeService->contarCantidadVotosPorEleccion($eleccion) }}</p>
                    <p>Porcentaje de participación: {{ round($eeService->calcularPorcentajeParticipacionPorEleccion($eleccion), 2) }}%</p>
                </div>
                @php
                    $totalVotos = 0;
                    $totalPorcentajeVotos = 0;
                @endphp

                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Foto</th>
                            <th>Partido</th>
                            <th>Votos</th>
                            <th>Votos emitidos / Total de votos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partidoService->obtenerPartidosInscritosEnEleccion($eleccion) as $partido)
                            @php
                                $cantidadVotos = $eeService->contarCantidadVotosParaPartidoEnEleccion($eleccion, $partido);
                                $porcentajeVotos = $eeService->calcularPorcentajeVotosParaPartidoEnEleccion($eleccion, $partido);
                                $totalVotos += $cantidadVotos;
                                $totalPorcentajeVotos += $porcentajeVotos;
                            @endphp
                            <tr>
                                <td><img class="h-24 img-partido" @if($partido->foto?->ruta) src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $partido->foto?->ruta))) }}" @else src="{{ public_path('img/undraw_profile.svg') }}" @endif alt=""></td>
                                <td>{{ $partido->partido }}</td>
                                <td>{{ $cantidadVotos }}</td>
                                <td>{{ round($porcentajeVotos, 2) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay partidos registrados en esta elección</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th>Total</th>
                            <th>{{ $totalVotos }}</th>
                            <th>{{ round($totalPorcentajeVotos, 2) }}%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @foreach ($areas as $area)
                <h2 class="font-bold text-xl my-2">{{ $area->area }}</h2>
                <div>
                    <p>Electores habilitados: {{ $eeService->contarElectoresHabilitadosPorEleccionYArea($eleccion, $area) }}</p>
                    <p>Electores que han votado: {{ $eeService->contarCantidadVotosPorEleccionYArea($eleccion, $area) }}</p>
                    <p>Porcentaje de participación: {{ round($eeService->calcularPorcentajeParticipacionPorEleccionYArea($eleccion, $area), 2) }}%</p>
                </div>
                @foreach($area->cargos as $cargo)
                @php
                    $totalVotosMismaArea = 0;
                    $totalVotosAreaExterna = 0;
                    $totalVotosPonderados = 0;
                    $totalPorcentajeVotosMismaArea = 0;
                    $totalPorcentajeVotosAreaExterna = 0;
                    $totalPorcentajeVotosTotales = 0;
                    $totalPorcentajeVotosEmitidosParaCargo = 0;
                @endphp
                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <h3 class="text-lg my-2">Para el cargo de {{ $cargo->cargo }}</h3>
                    <thead class="thead-light">
                        <tr>
                            <th>Foto</th>
                            <th>Candidato</th>
                            <th>Votos por la área de origen</th>
                            <th>Votos por otras áreas</th>
                            <th>Votos ponderados</th>
                            <th>% votos por área de origen</th>
                            <th>% votos por otras áreas</th>
                            <th>Votos emitidos / Total de votos</th>
                            <th>Votos ponderados / Total de votos ponderados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidatoService->obtenerCandidatosPorCargoEnEleccion($cargo, $eleccion) as $candidato)
                            @php
                                $cantidadVotosMismaArea = $eeService->contarCantidadVotosMismaAreaParaCandidato($eleccion, $candidato);
                                $cantidadVotosAreaExterna = $eeService->contarCantidadVotosAreaExternaParaCandidato($eleccion, $candidato);
                                $cantidadVotosPonderados = $eeService->contarCantidadVotosPonderadosParaCandidato($eleccion, $candidato);
                                $porcentajeVotosMismaArea = $eeService->calcularPorcentajeVotosMismaAreaParaCandidato($eleccion, $candidato);
                                $porcentajeVotosAreaExterna = $eeService->calcularPorcentajeVotosAreaExternaParaCandidato($eleccion, $candidato);
                                $porcentajeVotosTotales = $eeService->calcularPorcentajeVotosTotalesParaCandidato($eleccion, $candidato);
                                $porcentajeVotosEmitidosParaCargo = $eeService->calcularPorcentajeVotosPonderadosParaCandidatoEnCargo($eleccion, $candidato);

                                $porcentajeVotosMismaArea = $porcentajeVotosMismaArea != -1 ? $porcentajeVotosMismaArea : 0;
                                $porcentajeVotosAreaExterna = $porcentajeVotosAreaExterna != -1 ? $porcentajeVotosAreaExterna : 0;
                                $porcentajeVotosTotales = $porcentajeVotosTotales != -1 ? $porcentajeVotosTotales : 0;
                                $porcentajeVotosEmitidosParaCargo = $porcentajeVotosEmitidosParaCargo != -1 ? $porcentajeVotosEmitidosParaCargo : 0;

                                $totalVotosMismaArea += $cantidadVotosMismaArea;
                                $totalVotosAreaExterna += $cantidadVotosAreaExterna;
                                $totalVotosPonderados += $cantidadVotosPonderados;
                                $totalPorcentajeVotosMismaArea += $porcentajeVotosMismaArea;
                                $totalPorcentajeVotosAreaExterna += $porcentajeVotosAreaExterna;
                                $totalPorcentajeVotosTotales += $porcentajeVotosTotales;
                                $totalPorcentajeVotosEmitidosParaCargo += $porcentajeVotosEmitidosParaCargo;
                            @endphp
                            <tr>
                                <td><img class="w-16 img-candidato" @if($candidato->usuario->perfil?->foto?->ruta) src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $candidato->usuario->perfil?->foto?->ruta))) }}" @else src="{{ public_path('img/undraw_profile.svg') }}" @endif alt=""></td>
                                <td>{{ $candidato->usuario->perfil?->obtenerNombreApellido() }}</td>
                                <td>{{ $cantidadVotosMismaArea }}</td>
                                <td>{{ $cantidadVotosAreaExterna }}</td>
                                <td>{{ $cantidadVotosPonderados }}</td>
                                <td>{{ round($porcentajeVotosMismaArea, 2) }}%</td>
                                <td>{{ round($porcentajeVotosAreaExterna, 2) }}%</td>
                                <td>{{ round($porcentajeVotosTotales, 2) }}%</td>
                                <td>{{ round($porcentajeVotosEmitidosParaCargo, 2) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    No hay candidatos inscritos en la elección
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>                            
                            <th>Total</th>
                            <th>{{ $totalVotosMismaArea }}</th>
                            <th>{{ $totalVotosAreaExterna }}</th>
                            <th>{{ $totalVotosPonderados }}</th>
                            <th>{{ round($totalPorcentajeVotosMismaArea, 2) }}%</th>
                            <th>{{ round($totalPorcentajeVotosAreaExterna, 2) }}%</th>
                            <th>{{ round($totalPorcentajeVotosTotales, 2) }}%</th>
                            <th>{{ round($totalPorcentajeVotosEmitidosParaCargo, 2) }}%</th>
                        </tr>
                    </tfoot>
                </table>
                @endforeach
                @endforeach
            </div>
        </div>
    </div>


</body>
</html>