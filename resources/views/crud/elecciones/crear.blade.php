@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="mb-4">
    <h5 class="font-weight-bold mb-0">Nuevo Proceso</h5>
  </div>

  <form method="POST" action="{{ route('crud.elecciones.crear') }}">
@csrf

<div class="form-group">
  <label class="small font-weight-bold">Nombre</label>
  <input
    type="text"
    name="titulo"
    class="form-control"
    required
    value="{{ old('titulo') }}">
</div>

<div class="form-group">
  <label class="small font-weight-bold">Descripción</label>
  <textarea
    name="descripcion"
    class="form-control"
    required>{{ old('descripcion') }}</textarea>
</div>

<div class="form-group">
  <label class="small font-weight-bold">Fecha inicio</label>
  <input
    type="datetime-local"
    name="fechaInicio"
    class="form-control"
    required>
</div>

<div class="form-group">
  <label class="small font-weight-bold">Fecha cierre</label>
  <input
    type="datetime-local"
    name="fechaCierre"
    class="form-control"
    required>
</div>

<div class="text-right mt-4">
  <button class="btn btn-primary px-4">
    Crear Elección
  </button>
</div>

</form>


</div>
@endsection
