@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Carrera</h1>
        <a href="{{ route('crud.carrera.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="editCarreraForm" action="{{ route('crud.carrera.editar', $carrera->getKey()) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="carrera">Nombre de la Carrera</label>
                    <input type="text" class="form-control" id="carrera" name="carrera" value="{{ $carrera->carrera }}" required maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Carrera
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editCarreraForm').addEventListener('submit', function(e) {
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
            window.location.href = '{{ route("crud.carrera.ver") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al actualizar la carrera');
    });
});
</script>
@endsection
