@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Propuesta de Partido</h1>
        <a href="{{ route('crud.propuesta_partido.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('crud.propuesta_partido.editar', $m->idPropuesta) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="idEleccion">Elección</label>
                    <select class="form-control" id="idEleccion" name="idEleccion" required>
                        <option value="">Seleccione una elección</option>
                        @foreach($elecciones as $eleccion)
                            <option value="{{ $eleccion->idElecciones }}" {{ $m->partido->elecciones->contains('idElecciones', $eleccion->idElecciones) ? 'selected' : '' }}>{{ $eleccion->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idPartido">Partido</label>
                    <select class="form-control" id="idPartido" name="idPartido" required>
                        <option value="">Seleccione un partido</option>
                        @foreach($elecciones as $eleccion)
                            @if($eleccion->partidos)
                                @foreach($eleccion->partidos as $partido)
                                    <option value="{{ $partido->idPartido }}" {{ $m->idPartido == $partido->idPartido ? 'selected' : '' }}>
                                        {{ $partido->partido }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="propuesta">Propuesta</label>
                    <input type="text" class="form-control" id="propuesta" name="propuesta" value="{{ $m->propuesta }}" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required>{{ $m->descripcion }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Propuesta
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
