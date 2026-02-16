@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Propuesta de Candidato</h1>
        <a href="{{ route('crud.propuesta_candidato.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @include('components.error-message')

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('crud.propuesta_candidato.editar', $propuesta->idPropuesta) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="idEleccion">Elección</label>
                    <select class="form-control" id="idEleccion" name="idEleccion" required>
                        <option value="">Seleccione una elección</option>
                        @foreach($elecciones as $eleccion)
                            <option value="{{ $eleccion->idElecciones }}" @if ($propuesta->idElecciones == $eleccion->getKey()) selected @endif>{{ $eleccion->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idCandidato">Candidato</label>
                    <select class="form-control" id="idCandidato" name="idCandidato" required>
                        <option value="">Seleccione un candidato</option>
                        @foreach($candidatos as $candidato)
                        @php
                            $candidatoEleccion = $eleccionesService->obtenerCandidatoEleccion($candidato, $eleccion);
                        @endphp
                            <option value="{{ $candidato->idCandidato }}" @if ($propuesta->idCandidato == $candidato->getKey()) selected @endif>
                                {{ $candidato->usuario->perfil->obtenerNombreApellido() ?? $candidato->usuario->correo }}
                                ({{ $candidatoEleccion->partido?->partido ?? 'Sin partido' }} - {{ $candidatoEleccion->cargo->cargo }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="propuesta">Propuesta</label>
                    <input type="text" class="form-control" id="propuesta" name="propuesta" value="{{ $propuesta->propuesta }}" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required>{{ $propuesta->descripcion }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Propuesta
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
