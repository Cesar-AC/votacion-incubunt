@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Propuestas de Candidato</h1>
        <a href="{{ route('crud.propuesta_candidato.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Propuesta
        </a>
    </div>

    <div class="accordion" id="accordionElecciones">

        @forelse($elecciones as $eleccion)

            @php
                // Todas las propuestas de candidatos de la elección
                $propuestas = $eleccion->partidos->flatMap->candidatos->flatMap->propuestas;
                $total = $propuestas->count();
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
                            {{ $total }} propuestas
                        </span>
                    </h2>
                </div>

                <div id="collapse{{ $eleccion->idElecciones }}"
                     class="collapse"
                     data-parent="#accordionElecciones">

                    <div class="card-body">

                        @if($propuestas->count())
                            @foreach($eleccion->partidos as $partido)
                                @php
                                    $candidatosConPropuestas = $partido->candidatos->filter(fn($c) => $c->propuestas->count() > 0);
                                @endphp
                                @if($candidatosConPropuestas->count())
                                    <h4 class="text-primary mt-2">{{ $partido->partido }}</h4>

                                    @foreach($candidatosConPropuestas as $candidato)
                                        <h5 class="mt-3">
                                            {{
                                                $candidato->usuario->perfil
                                                    ? trim(
                                                        $candidato->usuario->perfil->nombre.' '.
                                                        $candidato->usuario->perfil->apellidoPaterno.' '.
                                                        $candidato->usuario->perfil->apellidoMaterno
                                                    )
                                                    : $candidato->usuario->correo
                                            }}
                                            ({{ $candidato->cargo->cargo }})
                                        </h5>

                                        <ul class="list-group mb-3">
                                            @foreach($candidato->propuestas as $propuesta)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $propuesta->propuesta }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $propuesta->descripcion }}</small>
                                                    </div>

                                                    <div>
                                                        <a href="{{ route('crud.propuesta_candidato.editar', $propuesta->idPropuesta) }}"
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <form action="{{ route('crud.propuesta_candidato.eliminar', $propuesta->idPropuesta) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('¿Desea eliminar esta propuesta?')">
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
                            @endforeach
                        @else
                            <p class="text-muted">No hay propuestas en esta elección</p>
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
