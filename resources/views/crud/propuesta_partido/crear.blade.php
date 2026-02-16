@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Propuesta de Partido</h1>
        <a href="{{ route('crud.propuesta_partido.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('crud.propuesta_partido.crear') }}" method="POST">
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
                    <label for="idPartido">Partido</label>
                    <select class="form-control" id="idPartido" name="idPartido" required disabled>
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
    const partidoSelect = document.getElementById('idPartido');
    partidoSelect.innerHTML = '<option value="">Cargando...</option>';
    partidoSelect.disabled = true;

    if (eleccionId) {
        fetch(`/api/elecciones/${eleccionId}/partidos`)
            .then(response => response.json())
            .then(data => {
                partidoSelect.innerHTML = '<option value="">Seleccione un partido</option>';
                data.forEach(partido => {
                    const option = document.createElement('option');
                    option.value = partido.idPartido;
                    option.textContent = partido.partido;
                    partidoSelect.appendChild(option);
                });
                partidoSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                partidoSelect.innerHTML = '<option value="">Error al cargar partidos</option>';
            });
    } else {
        partidoSelect.innerHTML = '<option value="">Primero seleccione una elección</option>';
    }
});
</script>
@endsection
