@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Detalles del Voto</h1>
        <a href="{{ route('crud.voto.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Voto</h6>
        </div>
        <div class="card-body">
            @include('components.error-message')
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Tipo de Voto</h5>
                    <p class="text-muted">{{ ucfirst($tipo) }}</p>
                </div>
                <div class="col-md-6">
                    <h5>ID del Voto</h5>
                    <p class="text-muted">{{ $tipo === 'candidato' ? $voto->idVotoCandidato : $voto->idVotoPartido }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Entidad Votada</h5>
                    @if($tipo === 'candidato')
                        <p class="text-muted">
                            {{ $voto->candidato->usuario->perfil->nombre }} 
                            {{ $voto->candidato->usuario->perfil->apellidoPaterno }} 
                            {{ $voto->candidato->usuario->perfil->apellidoMaterno }}
                        </p>
                    @else
                        <p class="text-muted">{{ $voto->partido->partido }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5>Elección</h5>
                    <p class="text-muted">{{ $voto->eleccion->nombre }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Tipo de Ponderación</h5>
                    <p class="text-muted">
                        @if($voto->idTipoVoto == 1)
                            Misma Área
                        @elseif($voto->idTipoVoto == 2)
                            Otra Área
                        @else
                            {{ $voto->tipoVoto->nombre ?? 'N/A' }}
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Fecha de Creación</h5>
                    <p class="text-muted">{{ $voto->created_at ?? 'N/A' }}</p>
                </div>
            </div>

            <hr>

            <div class="form-group mt-4">
                <a href="{{ route('crud.voto.editar', $tipo === 'candidato' ? $voto->idVotoCandidato : $voto->idVotoPartido) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="{{ route('crud.voto.eliminar', $tipo === 'candidato' ? $voto->idVotoCandidato : $voto->idVotoPartido) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('¿Desea eliminar este voto?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
                <a href="{{ route('crud.voto.ver') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
