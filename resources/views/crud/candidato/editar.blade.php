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
                    <label for="idPartido">Partido</label>
                    <select class="form-control" id="idPartido" name="idPartido" required>
                        <option value="">Seleccione un partido</option>
                        @foreach($partidos as $partido)
                            <option value="{{ $partido->idPartido }}" {{ $candidato->idPartido == $partido->idPartido ? 'selected' : '' }}>{{ $partido->partido }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="idCargo">Cargo</label>
                    <select class="form-control" id="idCargo" name="idCargo" required>
                        <option value="">Seleccione un cargo</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->idCargo }}" {{ $candidato->idCargo == $cargo->idCargo ? 'selected' : '' }}>{{ $cargo->cargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="idUsuario">Usuario</label>
                    <select class="form-control" id="idUsuario" name="idUsuario" required>
                        <option value="">Seleccione un usuario</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->idUser }}" {{ $candidato->idUsuario == $usuario->idUser ? 'selected' : '' }}>{{ $usuario->correo }}</option>
                        @endforeach
                    </select>
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
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("crud.candidato.ver") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al actualizar el candidato');
    });
});
</script>
@endsection
