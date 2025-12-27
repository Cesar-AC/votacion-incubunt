@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="mb-4">
    <h5 class="font-weight-bold mb-0">Nuevo Proceso</h5>
  </div>

  <!-- Form -->
  <div class="card shadow-sm mb-3">
    <div class="card-body">

      <!-- Nombre -->
      <div class="form-group">
        <label class="small font-weight-bold">Nombre</label>
        <input type="text" class="form-control" placeholder="Nombre de la elección">
      </div>

      <!-- Padrón -->
      <div class="form-group">
        <label class="small font-weight-bold">Padrón Electoral</label>
        <select class="form-control">
          <option>Elecciones 2024</option>
          <option>Elecciones 2025</option>
        </select>
      </div>

      <!-- Candidatos y Roles -->
      <div class="form-group">
        <label class="small font-weight-bold">Candidatos y Roles</label>

        <div class="row">
          <div class="col-6 mb-2">
            <input type="text" class="form-control" placeholder="Puesto">
          </div>
          <div class="col-6 mb-2">
            <select class="form-control">
              <option>Usuario</option>
              <option>Representante RR.HH</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Selección de Candidato -->
      <div class="form-group">
        <label class="small font-weight-bold">Selecciona Candidatos</label>

        <div class="input-group">
          <select class="form-control">
            <option>Juan Manrique Torres</option>
            <option>María López</option>
          </select>
          <div class="input-group-append">
            <button class="btn btn-primary">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Resumen Candidatos -->
      <div class="form-group">
        <label class="small font-weight-bold">Resumen Candidatos</label>

        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Juan Manrique Torres
            <button class="btn btn-sm btn-outline-danger">
              <i class="fas fa-trash"></i>
            </button>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            María López
            <button class="btn btn-sm btn-outline-danger">
              <i class="fas fa-trash"></i>
            </button>
          </li>
        </ul>
      </div>

      <!-- Botón -->
      <div class="text-right mt-4">
        <button class="btn btn-primary px-4">
          Agregar Elección
        </button>
      </div>

    </div>
  </div>

</div>
@endsection
