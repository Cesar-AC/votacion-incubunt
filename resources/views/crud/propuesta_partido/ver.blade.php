@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Propuestas de Partido</h1>
        <a href="{{ route('crud.propuesta_partido.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Propuesta
        </a>
    </div>

    <div class="accordion" id="accordionElecciones">

        @forelse($elecciones as $eleccion)

            @php
                // Todas las propuestas de partidos de la elección
                $propuestas = $eleccion->partidos->flatMap->propuestas;
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
                                @if($partido->propuestas->count())
                                    <h4 class="text-primary mt-2">{{ $partido->partido }}</h4>

                                    <ul class="list-group mb-3">
                                        @foreach($partido->propuestas as $propuesta)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $propuesta->propuesta }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $propuesta->descripcion }}</small>
                                                </div>

                                                <div>
                                                    <a href="{{ route('crud.propuesta_partido.editar', $propuesta->idPropuesta) }}"
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('crud.propuesta_partido.eliminar', $propuesta->idPropuesta) }}"
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
