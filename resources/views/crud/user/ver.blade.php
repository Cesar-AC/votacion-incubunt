@extends('layouts.admin')
@section('content')
<div class="container-fluid px-3">

    <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Gestionar Usuarios</h5>
    <a href="{{ route('crud.user.crear') }}" class="btn btn-primary btn-sm shadow">
      Nuevo Usuario
    </a>
  </div>

  <!-- Buscador -->
  <div class="mb-4">
    <div class="input-group shadow-sm rounded-pill overflow-hidden">

      <!-- Input -->
      <input type="text"
             class="form-control border-0"
             placeholder="Buscar"
             aria-label="Buscar">

      <!-- Limpiar -->
      <div class="input-group-append">
        <button class="btn btn-light border-0">
          <i class="fas fa-times-circle text-muted"></i>
        </button>
      </div>

      <!-- Filtro -->
      <div class="input-group-append">
        <button class="btn btn-primary">
          <i class="fas fa-filter"></i>
        </button>
      </div>

    </div>
  </div>

  <!-- Usuario 1 -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="d-flex justify-content-between">

        <!-- Info -->
        <div>
          <h6 class="font-weight-bold mb-1">Juan Pérez</h6>
          <small class="text-muted d-block mb-2">
            juan.perez@correo.com
          </small>

          <!-- Etiquetas -->
          <span class="badge badge-info mr-1">
            Grupo: Estudiantes
          </span>
          <span class="badge badge-primary">
            Rol: Votante
          </span>
        </div>

        <!-- Acciones -->
        <div class="text-right">
          <a href="#" class="btn btn-outline-primary btn-sm mb-1">
            <i class="fas fa-edit"></i> Edit
          </a><br>
          <a href="#" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash"></i> Delete
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Usuario 2 -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="d-flex justify-content-between">

        <div>
          <h6 class="font-weight-bold mb-1">María López</h6>
          <small class="text-muted d-block mb-2">
            maria.lopez@correo.com
          </small>

          <span class="badge badge-warning mr-1">
            Grupo: Docentes
          </span>
          <span class="badge badge-success">
            Rol: Moderador
          </span>
        </div>

        <div class="text-right">
          <a href="#" class="btn btn-outline-primary btn-sm mb-1">
            <i class="fas fa-edit"></i> Edit
          </a><br>
          <a href="#" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash"></i> Delete
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Usuario 3 -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="d-flex justify-content-between">

        <div>
          <h6 class="font-weight-bold mb-1">Carlos Rivas</h6>
          <small class="text-muted d-block mb-2">
            carlos.rivas@correo.com
          </small>

          <span class="badge badge-secondary mr-1">
            Grupo: Administrativos
          </span>
          <span class="badge badge-danger">
            Rol: Administrador
          </span>
        </div>

        <div class="text-right">
          <a href="#" class="btn btn-outline-primary btn-sm mb-1">
            <i class="fas fa-edit"></i> Edit
          </a><br>
          <a href="#" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash"></i> Delete
          </a>
        </div>

      </div>
    </div>
  </div>

</div>
@endsection