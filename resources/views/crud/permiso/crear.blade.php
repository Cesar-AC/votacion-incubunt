@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Permiso</h1>
        <a href="{{ route('crud.permiso.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="createPermisoForm" action="{{ route('crud.permiso.crear') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="permiso">Nombre del Permiso</label>
                    <input type="text" class="form-control" id="permiso" name="permiso" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crear Permiso
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('createPermisoForm').addEventListener('submit', function(e) {
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
            window.location.href = '{{ route("crud.permiso.ver") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al crear el permiso');
    });
});
</script>
@endsection
