@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="mb-4">
    <h5 class="font-weight-bold mb-0">Editar Proceso</h5>
  </div>

  <form method="POST" action="{{ route('crud.elecciones.editar', $eleccion->getKey()) }}">
@csrf
@method('PUT')

<div class="form-group">
  <label class="small font-weight-bold">Nombre</label>
  <input
    type="text"
    name="titulo"
    class="form-control"
    required
    value="{{ old('titulo', $eleccion->titulo) }}">
</div>

<div class="form-group">
  <label class="small font-weight-bold">Descripción</label>
  <textarea
    name="descripcion"
    class="form-control"
    required>{{ old('descripcion', $eleccion->descripcion) }}</textarea>
</div>

<div class="form-group">
  <label class="small font-weight-bold">Fecha inicio</label>
  <input
    type="datetime-local"
    name="fechaInicio"
    class="form-control"
    required
    value="{{ old('fechaInicio', $eleccion->fechaInicio->format('Y-m-d\TH:i')) }}">
</div>

<div class="form-group">
  <label class="small font-weight-bold">Fecha cierre</label>
  <input
    type="datetime-local"
    name="fechaCierre"
    class="form-control"
    required
    value="{{ old('fechaCierre', $eleccion->fechaCierre->format('Y-m-d\TH:i')) }}">
</div>

<div class="form-group">
  <label class="small font-weight-bold">Estado</label>
  <select name="idEstado" class="form-control" required>
    @foreach($estados as $estado)
      <option value="{{ $estado->idEstado }}" {{ $eleccion->idEstado == $estado->idEstado ? 'selected' : '' }}>
        {{ $estado->estado }}
      </option>
    @endforeach
  </select>
</div>

<div class="text-right mt-4">
  <button class="btn btn-primary px-4">
    Actualizar Elección
  </button>
</div>

</form>


</div>
@endsection
