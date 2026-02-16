@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  @if (!isset($partido))
    <div class="alert alert-warning">
      No se encontró información del partido.
    </div>
  @else

  <!-- Header -->
  <form method="POST" action="{{ route('crud.partido.editar', $partido->idPartido) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="font-weight-bold mb-0">Editar Partido</h5>
      <button type="submit" class="btn btn-primary btn-sm shadow">
        Actualizar
      </button>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
      <div class="card-body">

        <!-- Nombre -->
        <div class="form-group">
          <label class="small font-weight-bold">Nombre del Partido</label>
          <input type="text"
                 name="partido"
                 class="form-control @error('partido') is-invalid @enderror"
                 value="{{ old('partido', $partido->partido) }}">
          @error('partido')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- URL -->
        <div class="form-group">
          <label class="small font-weight-bold">URL del Partido</label>
          <input type="url"
                 name="urlPartido"
                 class="form-control @error('urlPartido') is-invalid @enderror"
                 value="{{ old('urlPartido', $partido->urlPartido) }}">
          @error('urlPartido')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Descripción -->
        <div class="form-group mb-0">
          <label class="small font-weight-bold">Descripción</label>
          <textarea name="descripcion"
                    class="form-control @error('descripcion') is-invalid @enderror"
                    rows="3">{{ old('descripcion', $partido->descripcion) }}</textarea>
          @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Plan de Trabajo -->
        <div class="form-group mt-3">
          <label class="small font-weight-bold">Enlace al plan de trabajo (opcional)</label>
          <input type="url"
                 name="planTrabajo"
                 class="form-control @error('planTrabajo') is-invalid @enderror"
                 value="{{ old('planTrabajo', $partido->planTrabajo) }}">
          @error('planTrabajo')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        @include('components.previewable-upload-photo-for-crud', ['foto' => $partido->obtenerFotoURL()])

        <!-- Elecciones -->
        <div class="form-group">
          <label class="small font-weight-bold">Elecciones asociadas (opcional)</label>
          <select name="elecciones[]" multiple
                  class="form-control @error('elecciones') is-invalid @enderror">
            @php $seleccionadas = $partido->elecciones->pluck('idElecciones')->toArray(); @endphp
            @foreach($elecciones as $eleccion)
              <option value="{{ $eleccion->idElecciones }}"
                {{ in_array($eleccion->idElecciones, old('elecciones', $seleccionadas)) ? 'selected' : '' }}>
                {{ $eleccion->titulo }}
              </option>
            @endforeach
          </select>
          @error('elecciones')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

      </div>
    </div>
  </form>

  @endif
</div>
@endsection
