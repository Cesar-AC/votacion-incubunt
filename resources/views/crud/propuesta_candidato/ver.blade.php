@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    @include('components.error-message')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Propuestas de Candidato</h1>
        <a href="{{ route('crud.propuesta_candidato.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Propuesta
        </a>
    </div>

    <div id="accordionElecciones">

        @forelse($elecciones as $eleccion)
            @php
                $candidatosEleccion = $eleccion->candidatoElecciones->pluck('candidato')->filter();
                $propuestas = $candidatosEleccion->flatMap(function ($candidato) use ($eleccion) {
                    return $candidato->propuestas->where('idElecciones', $eleccion->idElecciones);
                });
                $total = $propuestas->count();
            @endphp

            <div class="card shadow mb-2">
                <div class="card-header" style="cursor: pointer;" onclick="toggleAccordion({{ $eleccion->idElecciones }})">
                    <h2 class="mb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-chevron-right accordion-icon" id="icon-{{ $eleccion->idElecciones }}"></i>
                            <strong>{{ $eleccion->titulo }}</strong>
                        </div>

                        <span class="badge badge-primary">
                            {{ $total }} propuestas
                        </span>
                    </h2>
                </div>

                <div id="content-{{ $eleccion->idElecciones }}" style="display: none;">
                    <div class="card-body">

                        @if($propuestas->count())
                            @foreach($candidatosEleccion as $candidato)
                                @php
                                    $candidatoEleccion = $candidato->candidatoElecciones
                                        ->firstWhere('idElecciones', $eleccion->idElecciones);
                                    $cargoNombre = optional($candidatoEleccion?->cargo)->cargo ?? 'Sin cargo';
                                    $partidoNombre = $candidatoEleccion?->idPartido 
                                        ? (optional($candidatoEleccion?->partido)->partido ?? 'Partido sin nombre')
                                        : 'Candidato de Área';
                                    $propuestasCandidato = $candidato->propuestas
                                        ->where('idElecciones', $eleccion->idElecciones);
                                @endphp

                                @if($propuestasCandidato->count() > 0)
                                    <div class="mb-4">
                                        <h5 class="text-primary">
                                            {{
                                                $candidato->usuario->perfil
                                                    ? trim(
                                                        $candidato->usuario->perfil->nombre.' '.
                                                        $candidato->usuario->perfil->apellidoPaterno.' '.
                                                        $candidato->usuario->perfil->apellidoMaterno
                                                    )
                                                    : $candidato->usuario->correo
                                            }}
                                        </h5>
                                        <p class="text-muted mb-2">
                                            <strong>Área:</strong> {{ $candidatoEleccion->cargo->area->area }} | 
                                            <strong>Cargo:</strong> {{ $cargoNombre }} | 
                                            <strong>{{ $candidatoEleccion?->idPartido ? 'Partido' : 'Tipo' }}:</strong> {{ $partidoNombre }}
                                        </p>

                                        <ul class="list-group mb-3">
                                            @foreach($propuestasCandidato as $propuesta)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $propuesta->propuesta }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $propuesta->descripcion }}</small>
                                                    </div>

                                                    <div class="d-flex gap-2 align-items-center">
                                                        <a href="{{ route('crud.propuesta_candidato.editar', $propuesta->idPropuesta) }}"
                                                           class="btn btn-sm btn-warning btn-action">
                                                            <i class="fas fa-edit"></i>Editar
                                                        </a>

                                                        <form action="{{ route('crud.propuesta_candidato.eliminar', $propuesta->idPropuesta) }}"
                                                              method="POST"
                                                              style="display: inline-block;"
                                                              onsubmit="return confirm('¿Desea eliminar esta propuesta?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger btn-action">
                                                                <i class="fas fa-trash"></i>Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
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

@push('scripts')
<script>
function toggleAccordion(id) {
    const content = document.getElementById('content-' + id);
    const icon = document.getElementById('icon-' + id);
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        content.style.display = 'none';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}
</script>
@endpush

@push('styles')
<style>
.accordion-icon {
    transition: transform 0.3s ease;
    margin-right: 10px;
}

.btn-action {
    white-space: nowrap;
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
}

.btn-action i {
    font-size: 0.875rem;
}

.btn-danger.btn-action {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.btn-danger.btn-action:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
}

.btn-warning.btn-action {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-warning.btn-action:hover {
    background-color: #e0a800 !important;
    border-color: #d39e00 !important;
}
</style>
@endpush
@endsection