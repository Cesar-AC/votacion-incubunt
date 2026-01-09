@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Candidatos</h1>
        <a href="{{ route('crud.candidato.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Candidato
        </a>
    </div>

    <div class="accordion" id="accordionElecciones">

        @forelse($elecciones as $eleccion)

            @php
                // Todos los candidatos de la elección
                $candidatos = $eleccion->partidos->flatMap->candidatos;

                // Separación por tipo real
                $individuales = $candidatos->filter(fn($c) => $c->cargo && $c->cargo->idArea != 1);
                $grupales     = $candidatos->filter(fn($c) => $c->cargo && $c->cargo->idArea == 1);

                $total = $candidatos->count();
            @endphp

            <div class="card shadow mb-2">
                <div class="card-header" id="heading{{ $eleccion->idElecciones }}">
                    <h2 class="mb-0 d-flex justify-content-between align-items-center">
                        <button class="btn btn-link text-left" type="button"
                                data-toggle="collapse"
                                data-target="#collapse{{ $eleccion->idElecciones }}">
                            <strong>{{ $eleccion->titulo }}</strong>
                        </button>

                        <span class="badge badge-primary">
                            {{ $total }} candidatos
                        </span>
                    </h2>
                </div>

                <div id="collapse{{ $eleccion->idElecciones }}"
                     class="collapse"
                     data-parent="#accordionElecciones">

                    <div class="card-body">

                        {{-- =========================
                             CANDIDATOS INDIVIDUALES
                        ========================== --}}
                        @if($individuales->count())
                            <h4 class="text-primary mt-2">Cargos Individuales</h4>

                            @foreach($individuales->groupBy(fn($c) => $c->cargo->cargo) as $cargoNombre => $grupo)
                                <h5 class="mt-3">{{ $cargoNombre }}</h5>

                                <ul class="list-group mb-3">
                                    @foreach($grupo as $candidato)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>
                                                    {{
                                                        $candidato->usuario->perfil
                                                            ? trim(
                                                                $candidato->usuario->perfil->nombre.' '.
                                                                $candidato->usuario->perfil->apellidoPaterno.' '.
                                                                $candidato->usuario->perfil->apellidoMaterno
                                                            )
                                                            : $candidato->usuario->correo
                                                    }}
                                                </strong>
                                            </div>

                                            <div>
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
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @endif

                        {{-- =========================
                             JUNTA DIRECTIVA (GRUPAL)
                        ========================== --}}
                        @if($grupales->count())
                            <h4 class="text-primary mt-4">Junta Directiva</h4>

                            @foreach($grupales->groupBy(fn($c) => $c->cargo->cargo) as $cargoNombre => $grupoCargo)

                                <h5 class="mt-3">{{ $cargoNombre }}</h5>

                                @foreach($grupoCargo->groupBy(fn($c) => $c->partido->partido ?? 'Sin partido') as $partidoNombre => $grupoPartido)

                                    <div class="card mb-2">
                                        <div class="card-header py-2">
                                            🏛 <strong>{{ $partidoNombre }}</strong>
                                        </div>

                                        <ul class="list-group list-group-flush">
                                            @foreach($grupoPartido as $candidato)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        {{
                                                            $candidato->usuario->perfil
                                                                ? trim(
                                                                    $candidato->usuario->perfil->nombre.' '.
                                                                    $candidato->usuario->perfil->apellidoPaterno.' '.
                                                                    $candidato->usuario->perfil->apellidoMaterno
                                                                )
                                                                : $candidato->usuario->correo
                                                        }}
                                                    </div>

                                                    <div>
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
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                @endforeach
                            @endforeach
                        @endif

                        @if(!$total)
                            <p class="text-muted">No hay candidatos en esta elección</p>
                        @endif

                    </div>
                </div>
            </div>

        @empty
            <div class="alert alert-info">
                No hay elecciones registradas
            </div>
        @endforelse

    </div>
</div>
@endsection
