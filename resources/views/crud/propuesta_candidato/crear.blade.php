@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Propuesta de Candidato</h1>
        <a href="{{ route('crud.propuesta_candidato.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('crud.propuesta_candidato.crear') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="idEleccion">Elección</label>
                    <select class="form-control" id="idEleccion" name="idEleccion" required>
                        <option value="">Seleccione una elección</option>
                        @foreach($elecciones as $eleccion)
                            <option value="{{ $eleccion->idElecciones }}">{{ $eleccion->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idCandidato">Candidato</label>
                    <select class="form-control" id="idCandidato" name="idCandidato" required disabled>
                        <option value="">Primero seleccione una elección</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="propuesta">Propuesta</label>
                    <input type="text" class="form-control" id="propuesta" name="propuesta" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crear Propuesta
                </button>
            </form>
        </div>
    </div>

</div>

<script>
document.getElementById('idEleccion').addEventListener('change', function() {
    const eleccionId = this.value;
    const candidatoSelect = document.getElementById('idCandidato');
    candidatoSelect.innerHTML = '<option value="">Cargando...</option>';
    candidatoSelect.disabled = true;

    if (eleccionId) {
        fetch(`/api/elecciones/${eleccionId}/candidatos`)
            .then(response => response.json())
            .then(data => {
                candidatoSelect.innerHTML = '<option value="">Seleccione un candidato</option>';
                data.forEach(candidato => {
                    const option = document.createElement('option');
                    option.value = candidato.idCandidato;
                    option.textContent = candidato.nombre + ' (' + candidato.partido + ' - ' + candidato.cargo + ')';
                    candidatoSelect.appendChild(option);
                });
                candidatoSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                candidatoSelect.innerHTML = '<option value="">Error al cargar candidatos</option>';
            });
    } else {
        candidatoSelect.innerHTML = '<option value="">Primero seleccione una elección</option>';
    }
});
</script>
@endsection
