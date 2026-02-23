@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Cargo</h1>
        <a href="{{ route('crud.cargo.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="editCargoForm" action="{{ route('crud.cargo.editar', $cargo->getKey()) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="cargo">Nombre del Cargo</label>
                    <input type="text" class="form-control" id="cargo" name="cargo" value="{{ $cargo->cargo }}" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="idArea">Área</label>
                    <select class="form-control" id="idArea" name="idArea" required>
                        <option value="">Seleccione un área</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->idArea }}" {{ $cargo->idArea == $area->idArea ? 'selected' : '' }}>{{ $area->area }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Cargo
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editCargoForm').addEventListener('submit', function(e) {
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
            window.location.href = '{{ route("crud.cargo.ver") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al actualizar el cargo');
    });
});
</script>
@endsection
