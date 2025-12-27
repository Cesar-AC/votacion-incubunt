@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Nuevo Padrón</h5>
    <button class="btn btn-primary btn-sm shadow">
      Guardar
    </button>
  </div>

  <!-- Nombre del padrón -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="form-group mb-0">
        <label class="small font-weight-bold">Elección</label>
        <input type="text"
               class="form-control"
               placeholder="Ej. Elecciones 2025">
      </div>
    </div>
  </div>

  <!-- Buscador -->
  <div class="mb-3">
    <div class="input-group shadow-sm rounded-pill overflow-hidden">
      <input type="text"
             class="form-control border-0"
             placeholder="Buscar">
      <div class="input-group-append">
        <button class="btn btn-light border-0">
          <i class="fas fa-times-circle text-muted"></i>
        </button>
      </div>
      <div class="input-group-append">
        <button class="btn btn-primary">
          <i class="fas fa-filter"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Lista de personas -->
  <div class="card shadow-sm">
    <div class="card-body p-2">

      <!-- Persona -->
      <div class="d-flex align-items-center p-2 border-bottom">
        <input type="checkbox" class="mr-2">

        <div class="rounded-circle bg-secondary mr-3"
             style="width:40px; height:40px;"></div>

        <div class="flex-grow-1">
          <div class="font-weight-bold">Juan Manrique Torres</div>
          <small class="text-muted">
            GTH · <span class="badge badge-success">Elector</span>
          </small>
        </div>
      </div>

      <!-- Persona seleccionada -->
      <div class="d-flex align-items-center p-2 border-bottom bg-light">
        <input type="checkbox" class="mr-2" checked>

        <div class="rounded-circle bg-secondary mr-3"
             style="width:40px; height:40px;"></div>

        <div class="flex-grow-1">
          <div class="font-weight-bold">Juan Manrique Torres</div>
          <small class="text-muted">
            GTH · <span class="badge badge-success">Elector</span>
          </small>
        </div>
      </div>

      <!-- Más items -->
      <div class="d-flex align-items-center p-2 border-bottom bg-light">
        <input type="checkbox" class="mr-2" checked>

        <div class="rounded-circle bg-secondary mr-3"
             style="width:40px; height:40px;"></div>

        <div class="flex-grow-1">
          <div class="font-weight-bold">Juan Manrique Torres</div>
          <small class="text-muted">
            GTH · <span class="badge badge-success">Elector</span>
          </small>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
