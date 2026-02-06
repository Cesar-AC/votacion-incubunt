@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Voto</h1>
        <a href="{{ route('crud.voto.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulario de Edición</h6>
        </div>
        <div class="card-body">
            @include('components.error-message')
            
            <form method="POST" action="{{ route('crud.voto.actualizar', $tipo === 'candidato' ? $voto->idVotoCandidato : $voto->idVotoPartido) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="tipoEntidad">Tipo de Entidad</label>
                    <input type="text" id="tipoEntidad" class="form-control" value="{{ ucfirst($tipo) }}" disabled>
                </div>

                @if($tipo === 'candidato')
                    <div class="form-group">
                        <label for="entidad">Candidato</label>
                        <input type="text" id="entidad" class="form-control" 
                               value="{{ $voto->candidato->usuario->perfil->nombre }} {{ $voto->candidato->usuario->perfil->apellidoPaterno }} {{ $voto->candidato->usuario->perfil->apellidoMaterno }}" 
                               disabled>
                    </div>
                @else
                    <div class="form-group">
                        <label for="entidad">Partido</label>
                        <input type="text" id="entidad" class="form-control" 
                               value="{{ $voto->partido->partido }}" 
                               disabled>
                    </div>
                @endif

                <div class="form-group">
                    <label for="eleccion">Elección</label>
                    <input type="text" id="eleccion" class="form-control" 
                           value="{{ $voto->eleccion->nombre }}" 
                           disabled>
                </div>

                <div class="form-group">
                    <label for="idTipoVoto">Tipo de Ponderación*</label>
                    <select id="idTipoVoto" name="idTipoVoto" class="form-control" required>
                        <option value="">Seleccionar ponderación...</option>
                        <option value="1" @selected($voto->idTipoVoto == 1)>Misma Área</option>
                        <option value="2" @selected($voto->idTipoVoto == 2)>Otra Área</option>
                    </select>
                    @error('idTipoVoto')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('crud.voto.ver') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
