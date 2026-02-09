@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Nuevo Voto</h1>
        <a href="{{ route('crud.voto.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulario de Voto</h6>
        </div>
        <div class="card-body">
            @include('components.error-message')
            
            @if($elecciones->count() > 0)
                <form id="votoForm" method="POST" action="{{ route('crud.voto.guardar') }}">
                    @csrf

                    <div class="form-group">
                        <label for="idElecciones">Elección*</label>
                        <select id="idElecciones" name="idElecciones" class="form-control" required onchange="cambiarEleccion()">
                            <option value="">Seleccionar elección...</option>
                            @foreach($elecciones as $eleccion)
                                <option value="{{ $eleccion->idElecciones }}" @selected($eleccion->idElecciones == $eleccionActiva?->idElecciones)>
                                    {{ $eleccion->titulo }}
                                </option>
                            @endforeach
                        </select>
                        @error('idElecciones')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tipoVoto">Tipo de Voto*</label>
                        <select id="tipoVoto" name="tipo" class="form-control" required onchange="cambiarTipo()">
                            <option value="">Seleccionar tipo...</option>
                            <option value="candidato">Candidato</option>
                            <option value="partido">Partido</option>
                        </select>
                        @error('tipo')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Selector de Candidato -->
                    <div id="candidatoGroup" class="form-group" style="display: none;">
                        <label for="candidatoId">Candidato*</label>
                        <select id="candidatoId" class="form-control">
                            <option value="">Seleccionar candidato...</option>
                            @foreach($candidatos as $candidato)
                                <option value="{{ $candidato->idCandidato }}">
                                    {{ $candidato->usuario->perfil->nombre }} 
                                    {{ $candidato->usuario->perfil->apellidoPaterno }} 
                                    {{ $candidato->usuario->perfil->apellidoMaterno }}
                                </option>
                            @endforeach
                        </select>
                        @error('entidad_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Selector de Partido -->
                    <div id="partidoGroup" class="form-group" style="display: none;">
                        <label for="partidoId">Partido*</label>
                        <select id="partidoId" class="form-control">
                            <option value="">Seleccionar partido...</option>
                            @foreach($partidos as $partido)
                                <option value="{{ $partido->idPartido }}">
                                    {{ $partido->partido }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Campo oculto para enviar la entidad_id -->
                    <input type="hidden" name="entidad_id" id="entidad_id" value="">

                    <div class="form-group">
                        <label for="idTipoVoto">Tipo de Ponderación*</label>
                        <select id="idTipoVoto" name="idTipoVoto" class="form-control" required>
                            <option value="">Seleccionar ponderación...</option>
                            <option value="1">Misma Área</option>
                            <option value="2">Otra Área</option>
                        </select>
                        @error('idTipoVoto')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Crear Voto
                        </button>
                        <a href="{{ route('crud.voto.ver') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            @else
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> No hay elecciones disponibles en este momento.
                </div>
                <a href="{{ route('crud.voto.ver') }}" class="btn btn-secondary">
                    Volver
                </a>
            @endif
        </div>
    </div>
</div>

<script>
    // Datos de candidatos y partidos por elección
    const eleccionesData = {
        @foreach($elecciones as $eleccion)
            {{ $eleccion->idElecciones }}: {
                candidatos: {!! json_encode($eleccion->candidatos()->with('usuario.perfil')->get()->map(function($c) { return ['id' => $c->idCandidato, 'nombre' => $c->usuario->perfil->nombre . ' ' . $c->usuario->perfil->apellidoPaterno . ' ' . $c->usuario->perfil->apellidoMaterno]; })) !!},
                partidos: {!! json_encode($eleccion->partidos()->get()->map(function($p) { return ['id' => $p->idPartido, 'nombre' => $p->partido]; })) !!}
            },
        @endforeach
    };

    function cambiarEleccion() {
        const idElecciones = document.getElementById('idElecciones').value;
        const data = eleccionesData[idElecciones];

        if (data) {
            // Actualizar candidatos
            const candidatoSelect = document.getElementById('candidatoId');
            candidatoSelect.innerHTML = '<option value="">Seleccionar candidato...</option>';
            data.candidatos.forEach(function(candidato) {
                const option = document.createElement('option');
                option.value = candidato.id;
                option.textContent = candidato.nombre;
                candidatoSelect.appendChild(option);
            });

            // Actualizar partidos
            const partidoSelect = document.getElementById('partidoId');
            partidoSelect.innerHTML = '<option value="">Seleccionar partido...</option>';
            data.partidos.forEach(function(partido) {
                const option = document.createElement('option');
                option.value = partido.id;
                option.textContent = partido.nombre;
                partidoSelect.appendChild(option);
            });
        }
    }

    function cambiarTipo() {
        const tipo = document.getElementById('tipoVoto').value;
        const candidatoGroup = document.getElementById('candidatoGroup');
        const partidoGroup = document.getElementById('partidoGroup');
        const candidatoId = document.getElementById('candidatoId');
        const partidoId = document.getElementById('partidoId');

        candidatoGroup.style.display = 'none';
        partidoGroup.style.display = 'none';
        candidatoId.removeAttribute('required');
        partidoId.removeAttribute('required');

        if (tipo === 'candidato') {
            candidatoGroup.style.display = 'block';
            candidatoId.setAttribute('required', 'required');
        } else if (tipo === 'partido') {
            partidoGroup.style.display = 'block';
            partidoId.setAttribute('required', 'required');
        }
    }

    document.getElementById('votoForm').addEventListener('submit', function(e) {
        const idElecciones = document.getElementById('idElecciones').value;
        const tipo = document.getElementById('tipoVoto').value;
        const idTipoVoto = document.getElementById('idTipoVoto').value;
        const candidatoId = document.getElementById('candidatoId').value;
        const partidoId = document.getElementById('partidoId').value;

        if (!idElecciones || idElecciones === '') {
            e.preventDefault();
            alert('Por favor selecciona una elección');
            return;
        }

        if (!tipo || tipo === '') {
            e.preventDefault();
            alert('Por favor selecciona un tipo de voto');
            return;
        }

        // Seleccionar la entidad según el tipo
        let entidadId = '';
        if (tipo === 'candidato') {
            entidadId = candidatoId;
        } else if (tipo === 'partido') {
            entidadId = partidoId;
        }

        if (!entidadId || entidadId === '') {
            e.preventDefault();
            alert('Por favor selecciona una entidad para votar');
            return;
        }

        if (!idTipoVoto || idTipoVoto === '') {
            e.preventDefault();
            alert('Por favor selecciona un tipo de ponderación');
            return;
        }

        // Asignar el valor correcto al campo oculto
        document.getElementById('entidad_id').value = entidadId;
    });

    // Cargar datos al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        const idElecciones = document.getElementById('idElecciones').value;
        if (idElecciones) {
            cambiarEleccion();
        }
    });
</script>
@endsection
