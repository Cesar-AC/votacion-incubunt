<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary" id="titulo-eleccion">{{ $eleccion->titulo }}</h6>            
        <span class="badge badge-primary"></span>
        @if (isset($mostrarBotonExportar) && $mostrarBotonExportar)
            <a href="{{ route('crud.voto.reporte', $eleccion->getKey()) }}" class="btn btn-primary"><span class="fas fa-file-pdf mr-2"></span>Exportar reporte</a>
        @endif
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
                            <td><img class="h-24" src="{{ $partido->obtenerFotoURL() ?? asset('img/undraw_profile.svg') }}" alt=""></td>
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
                            <td><img class="w-16" src="{{ $candidato->usuario->perfil?->obtenerFotoURL() ?? asset('img/undraw_profile.svg') }}" alt=""></td>
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
