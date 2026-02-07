@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <form method="POST" action="{{ route('crud.partido.crear') }}" enctype="multipart/form-data">
    @csrf

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="font-weight-bold mb-0">Nuevo Partido</h5>
      <button type="submit" class="btn btn-primary btn-sm shadow">
        Guardar
      </button>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <!-- Nombre -->
        <div class="form-group">
          <label class="small font-weight-bold">Nombre del Partido</label>
          <input type="text"
                 name="partido"
                 class="form-control @error('partido') is-invalid @enderror"
                 value="{{ old('partido') }}">
          @error('partido')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- URL -->
        <div class="form-group">
          <label class="small font-weight-bold">URL del Partido</label>
          <input type="url"
                 name="urlPartido"
                 class="form-control @error('urlPartido') is-invalid @enderror"
                 value="{{ old('urlPartido') }}">
          @error('urlPartido')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Descripción -->
        <div class="form-group mb-0">
          <label class="small font-weight-bold">Descripción</label>
          <textarea name="descripcion"
                    class="form-control @error('descripcion') is-invalid @enderror"
                    rows="3">{{ old('descripcion') }}</textarea>
          @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Plan de Trabajo -->
        <div class="form-group mt-3">
          <label class="small font-weight-bold">Enlace al plan de trabajo (opcional)</label>
          <input type="url"
                 name="planTrabajo"
                 class="form-control @error('planTrabajo') is-invalid @enderror"
                 value="{{ old('planTrabajo') }}">
          @error('planTrabajo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Foto -->
        <div class="form-group mt-3">
          <label class="small font-weight-bold">Foto del Partido (opcional)</label>
          <input type="file"
                 name="foto"
                 accept="image/*"
                 class="form-control @error('foto') is-invalid @enderror"
                 value="{{ old('foto') }}">
          @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

      </div>
    </div>
  </form>

</div>
@endsection
