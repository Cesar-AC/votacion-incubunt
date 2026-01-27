@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Candidatos</h1>
        <a href="{{ route('crud.candidato.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Candidato
        </a>
    </div>

    @forelse($elecciones as $eleccion)

        @php
            // Candidatos individuales (via CandidatoEleccion - sin partido)
            $candidatosIndividuales = collect([]);
            if ($eleccion->candidatos) {
                $candidatosIndividuales = $eleccion->candidatos->filter(fn($c) => $c->idPartido === null);
            }

            // Candidatos grupales (via PartidoEleccion - con partido)
            $candidatosGrupales = collect([]);
            if ($eleccion->partidos) {
                $candidatosGrupales = $eleccion->partidos->flatMap(fn($partido) => $partido->candidatos);
            }

            // Unir todos los candidatos
            $candidatos = $candidatosIndividuales->merge($candidatosGrupales);

            // Separación por PARTIDO (null = individual, con valor = grupal)
            $individuales = $candidatos->filter(fn($c) => $c->idPartido === null);
            $grupales     = $candidatos->filter(fn($c) => $c->idPartido !== null);

            $total = $candidatos->count();
        @endphp

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ $eleccion->titulo }}</h6>
                <span class="badge badge-primary">{{ $total }} candidatos</span>
            </div>
            <div class="card-body">

                {{-- CANDIDATOS INDIVIDUALES --}}
                @if($individuales->count())
                    <h5 class="text-primary mb-3">Cargos Individuales</h5>

                    @foreach($individuales->groupBy(fn($c) => $c->cargo ? $c->cargo->cargo : 'Sin cargo') as $cargoNombre => $grupo)
                        <h6 class="mt-3 mb-2 text-secondary">{{ $cargoNombre }}</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Candidato</th>
                                        <th>Área</th>
                                        <th width="150">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grupo as $candidato)
                                        <tr>
                                            <td>
                                                @if($candidato->usuario && $candidato->usuario->perfil)
                                                    {{ trim(
                                                        $candidato->usuario->perfil->nombre.' '.
                                                        $candidato->usuario->perfil->apellidoPaterno.' '.
                                                        $candidato->usuario->perfil->apellidoMaterno
                                                    ) }}
                                                @elseif($candidato->usuario)
                                                    {{ $candidato->usuario->correo }}
                                                @else
                                                    Usuario no disponible
                                                @endif
                                            </td>
                                            <td>
                                                {{ $candidato->cargo && $candidato->cargo->area ? $candidato->cargo->area->area : 'N/A' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('crud.candidato.editar', $candidato->idCandidato) }}"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('crud.candidato.eliminar', $candidato->idCandidato) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Desea eliminar este candidato?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif

                {{-- JUNTA DIRECTIVA (GRUPAL) --}}
                @if($grupales->count())
                    <h5 class="text-primary mb-3 mt-4">Junta Directiva</h5>

                    @foreach($grupales->groupBy(fn($c) => $c->partido ? $c->partido->partido : 'Sin partido') as $partidoNombre => $grupoPartido)
                        <h6 class="mt-3 mb-2 text-secondary">{{ $partidoNombre }}</h6>

                        @foreach($grupoPartido->groupBy(fn($c) => $c->cargo ? $c->cargo->cargo : 'Sin cargo') as $cargoNombre => $grupoCargo)
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="w-50">Candidato</th>
                                            <th class="w-25">Cargo</th>
                                            <th class="w-25" width="150">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-active">
                                            <td colspan="3"><strong>{{ $cargoNombre }}</strong></td>
                                        </tr>
                                        @foreach($grupoCargo as $candidato)
                                            <tr>
                                                <td>
                                                    @if($candidato->usuario && $candidato->usuario->perfil)
                                                        {{ trim(
                                                            $candidato->usuario->perfil->nombre.' '.
                                                            $candidato->usuario->perfil->apellidoPaterno.' '.
                                                            $candidato->usuario->perfil->apellidoMaterno
                                                        ) }}
                                                    @elseif($candidato->usuario)
                                                        {{ $candidato->usuario->correo }}
                                                    @else
                                                        Usuario no disponible
                                                    @endif
                                                </td>
                                                <td>{{ $cargoNombre }}</td>
                                                <td>
                                                    <a href="{{ route('crud.candidato.editar', $candidato->idCandidato) }}"
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('crud.candidato.eliminar', $candidato->idCandidato) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('¿Desea eliminar este candidato?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endforeach
                @endif

                @if(!$total)
                    <p class="text-muted">No hay candidatos en esta elección</p>
                @endif

            </div>
        </div>

    @empty
        <div class="alert alert-info">
            No hay elecciones registradas
        </div>
    @endforelse

</div>
@endsection
