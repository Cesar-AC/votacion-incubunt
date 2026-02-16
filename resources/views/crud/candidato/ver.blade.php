@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Candidatos</h1>
        <a href="{{ route('crud.candidato.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Candidato
        </a>
    </div>

    {{-- Mensajes de error, advertencia y éxito --}}
    @include('components.error-message')

    @forelse($elecciones as $eleccion)
        @php
            $individuales = $eleccionesService->obtenerCandidatosIndividuales($eleccion);
            $grupales     = $eleccionesService->obtenerCandidatosMiembrosDePartido($eleccion);
            $partidos     = $partidoService->obtenerPartidosInscritosEnEleccion($eleccion);
        @endphp

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ $eleccion->titulo }}</h6>
                <span class="badge badge-primary">{{ $individuales->count() }} candidato(s), {{ $partidos->count() }} partido(s)</span>
            </div>
            <div class="card-body">
                @if (count($individuales) > 0)
                    <h3 class="p-2 mb-2 font-weight-bold text-secondary">Candidatos</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Área</th>
                                    <th>Cargo</th>
                                    <th>Plan de Trabajo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($individuales as $individual)
                                    @php
                                        $candidatoEleccion = $eleccionesService->obtenerCandidatoEleccion($individual, $eleccion);
                                    @endphp
                                    <tr>
                                        <td>{{ $individual->usuario->perfil?->obtenerNombreApellido() . ' <' . $individual->usuario->correo . '>' }}</td>
                                        <td>{{ $candidatoEleccion->cargo?->area?->area }}</td>
                                        <td>{{ $candidatoEleccion->cargo?->cargo }}</td>
                                        <td>{{ $individual->planTrabajo }}</td>
                                        <td>
                                            <a href="{{ route('crud.candidato.editar', [$eleccion->getKey(), $individual->getKey()]) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('crud.candidato.eliminar', [$eleccion->getKey(), $individual->getKey()]) }}"
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
                @else
                    <p class="text-center">No hay candidatos individuales para esta elección</p>
                @endif

                @if (count($grupales) > 0)
                    <h3 class="p-2 mb-2 font-weight-bold text-secondary">Junta Directiva</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Partido</th>
                                    <th>Cargo</th>
                                    <th>Plan de Trabajo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grupales as $grupal)
                                    @php
                                        $candidatoEleccion = $eleccionesService->obtenerCandidatoEleccion($grupal, $eleccion);
                                    @endphp
                                    <tr>
                                        <td>{{ $grupal->usuario->perfil?->obtenerNombreApellido() . ' <' . $grupal->usuario->correo . '>' }}</td>
                                        <td>{{ $candidatoEleccion->partido?->partido }}</td>
                                        <td>{{ $candidatoEleccion->cargo?->cargo }}</td>
                                        <td>{{ $grupal->planTrabajo }}</td>
                                        <td>
                                            <a href="{{ route('crud.candidato.editar', [$eleccion->getKey(), $grupal->getKey()]) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('crud.candidato.eliminar', [$eleccion->getKey(), $grupal->getKey()]) }}"
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
                @else 
                    <p class="text-center">No hay candidatos inscritos en partidos para esta elección</p>
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
