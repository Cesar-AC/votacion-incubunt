@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="mb-4">
    <h5 class="font-weight-bold mb-0">Nuevo Proceso</h5>
  </div>

  <!-- Errores -->
  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <form method="POST" action="{{ route('crud.elecciones.crear') }}">
    @csrf

    <div class="form-group">
      <label class="small font-weight-bold">Nombre</label>
      <input
        type="text"
        name="titulo"
        class="form-control @error('titulo') is-invalid @enderror"
        required
        value="{{ old('titulo') }}">
      @error('titulo')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label class="small font-weight-bold">Descripción</label>
      <textarea
        name="descripcion"
        class="form-control @error('descripcion') is-invalid @enderror"
        required>{{ old('descripcion') }}</textarea>
      @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label class="small font-weight-bold">Fecha inicio</label>
      <input
        type="datetime-local"
        name="fechaInicio"
        class="form-control @error('fechaInicio') is-invalid @enderror"
        required
        value="{{ old('fechaInicio') }}">
      @error('fechaInicio')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label class="small font-weight-bold">Fecha cierre</label>
      <input
        type="datetime-local"
        name="fechaCierre"
        class="form-control @error('fechaCierre') is-invalid @enderror"
        required
        value="{{ old('fechaCierre') }}">
      @error('fechaCierre')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="text-right mt-4">
      <a href="{{ route('crud.elecciones.ver') }}" class="btn btn-secondary px-4 mr-2">
        Volver
      </a>
      <button type="submit" class="btn btn-primary px-4">
        Crear Elección
      </button>
    </div>

  </form>

</div>

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Mostrar SweetAlert si hay errores
  @if ($errors->any())
    Swal.fire({
      icon: 'error',
      title: 'Error al crear la elección',
      html: `
        <ul style="text-align: left;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      `,
      confirmButtonText: 'Entendido'
    });
  @endif
</script>
@endpush

@endsection
