@extends('layouts.admin')

@section('title', 'Crear Propuesta de Candidato')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle"></i> Nueva Propuesta como Candidato
        </h1>
        <a href="{{ route('votante.propuestas_candidato.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Mensajes de Error -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Información del Candidato -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-user-circle"></i> Tu Candidatura
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Nombre:</strong> {{ $candidato->usuario?->perfil?->nombre }} {{ $candidato->usuario?->perfil?->apellidoPaterno }}</p>
            <p class="mb-2"><strong>Cargo:</strong> {{ $candidato->cargo->cargo ?? 'N/A' }}</p>
            <p class="mb-0"><strong>Partido:</strong> {{ $candidato->partido->partido ?? 'Independiente' }}</p>
        </div>
    </div>

    <!-- Formulario -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit"></i> Datos de la Propuesta
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('votante.propuestas_candidato.guardar') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="propuesta">Título de la Propuesta <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('propuesta') is-invalid @enderror" 
                           id="propuesta" 
                           name="propuesta" 
                           value="{{ old('propuesta') }}" 
                           placeholder="Ej: Implementar becas para estudiantes destacados"
                           maxlength="255"
                           required>
                    @error('propuesta')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Máximo 255 caracteres.</small>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="5" 
                              placeholder="Describe detalladamente tu propuesta..."
                              maxlength="1000"
                              required>{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Máximo 1000 caracteres. Quedan <span id="contador">1000</span> caracteres.</small>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Propuesta
                    </button>
                    <a href="{{ route('votante.propuestas_candidato.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('descripcion').addEventListener('input', function() {
    const maxLength = 1000;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    document.getElementById('contador').textContent = remaining;
});
</script>
@endsection
