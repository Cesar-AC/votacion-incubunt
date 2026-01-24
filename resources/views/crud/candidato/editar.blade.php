@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Candidato</h1>
        <a href="{{ route('crud.candidato.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="editCandidatoForm" action="{{ route('crud.candidato.editar', $candidato->getKey()) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="tipo">Tipo de Candidato</label>
                    <select class="form-control" id="tipo" name="tipo" required>
                        <option value="">Seleccione tipo</option>
                        <option value="Individual" {{ $candidato->idPartido === null ? 'selected' : '' }}>Individual (Director de Área)</option>
                        <option value="Grupal" {{ $candidato->idPartido !== null ? 'selected' : '' }}>Grupal (Junta Directiva)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="idUsuario">Usuario</label>
                    <select class="form-control" id="idUsuario" name="idUsuario" required>
                        <option value="">Seleccione un usuario</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->idUser }}" {{ $candidato->idUsuario == $usuario->idUser ? 'selected' : '' }}>
                                {{ $usuario->perfil ? trim($usuario->perfil->nombre . ' ' . $usuario->perfil->apellidoPaterno . ' ' . $usuario->perfil->apellidoMaterno) : $usuario->correo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idCargo">Cargo</label>
                    <select class="form-control" id="idCargo" name="idCargo" required>
                        <option value="">Seleccione un cargo</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->idCargo }}" {{ $candidato->idCargo == $cargo->idCargo ? 'selected' : '' }}>
                                {{ $cargo->cargo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="partidoDiv" style="display:none;">
                    <label for="idPartido">Partido</label>
                    <select class="form-control" id="idPartido" name="idPartido">
                        <option value="">Seleccione un partido</option>
                        @foreach($partidos as $partido)
                            <option value="{{ $partido->idPartido }}" {{ $candidato->idPartido == $partido->idPartido ? 'selected' : '' }}>
                                {{ $partido->partido }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="planTrabajo">Plan de Trabajo</label>
                    <textarea class="form-control" id="planTrabajo" name="planTrabajo" rows="4" placeholder="Ingrese el plan de trabajo (opcional)">{{ $candidato->idPartido ? ($candidato->partido->planTrabajo ?? '') : ($candidato->planTrabajo ?? '') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Candidato
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editCandidatoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const tipo = document.getElementById('tipo').value;
    const idPartido = document.getElementById('idPartido').value;

    // Validar que candidato Grupal tenga partido
    if (tipo === 'Grupal' && !idPartido) {
        alert('Los candidatos grupales requieren un partido');
        return;
    }

    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            alert('Candidato actualizado correctamente');
            window.location.href = '{{ route("crud.candidato.ver") }}';
        } else {
            return response.text().then(text => {
                throw new Error(text);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
});

// Mostrar/ocultar campo de partido según el tipo
document.getElementById('tipo').addEventListener('change', function() {
    const partidoDiv = document.getElementById('partidoDiv');
    const tipoSeleccionado = this.value;
    
    if (tipoSeleccionado === 'Grupal') {
        partidoDiv.style.display = 'block';
        document.getElementById('idPartido').required = true;
    } else {
        partidoDiv.style.display = 'none';
        document.getElementById('idPartido').required = false;
        document.getElementById('idPartido').value = '';
    }
});

// Inicializar al cargar
window.addEventListener('load', function() {
    const tipoActual = document.getElementById('tipo').value;
    const partidoDiv = document.getElementById('partidoDiv');
    
    if (tipoActual === 'Grupal') {
        partidoDiv.style.display = 'block';
    } else {
        partidoDiv.style.display = 'none';
    }
});
</script>
@endsection
