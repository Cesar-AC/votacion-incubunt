@extends('layouts.admin')

@section('content')
<div class="container-fluid px-2 px-md-4">

  <!-- Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <h5 class="font-weight-bold mb-2 mb-md-0">Gestionar Elecciones</h5>
        <a href="{{ route('crud.elecciones.crear') }}" class="btn btn-primary btn-sm shadow">
        Nueva elección
        </a>
  </div>

  <!-- Card Elección -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="row align-items-center">

        <!-- Info -->
        <div class="col-12 col-md-8 mb-2 mb-md-0">
          <h6 class="font-weight-bold mb-1">Elecciones 2025</h6>
          <small class="text-muted d-block">
            👥 50 participantes
          </small>
          <span class="badge badge-success mt-1">
            Activa
          </span>
        </div>

        <!-- Acciones -->
        <div class="col-12 col-md-4 text-md-right">
          <a href="#" class="btn btn-outline-primary btn-sm mb-1">
            <i class="fas fa-edit"></i> Editar
          </a>
          <a href="#" class="btn btn-outline-danger btn-sm mb-1">
            <i class="fas fa-trash"></i> Eliminar
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Card Elección -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="row align-items-center">

        <div class="col-12 col-md-8 mb-2 mb-md-0">
          <h6 class="font-weight-bold mb-1">Elecciones 2024</h6>
          <small class="text-muted d-block">
            👥 45 participantes
          </small>
          <span class="badge badge-secondary mt-1">
            Finalizada
          </span>
        </div>

        <div class="col-12 col-md-4 text-md-right">
          <a href="#" class="btn btn-outline-primary btn-sm mb-1">
            <i class="fas fa-edit"></i> Editar
          </a>
          <a href="#" class="btn btn-outline-danger btn-sm mb-1">
            <i class="fas fa-trash"></i> Eliminar
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Card Elección -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="row align-items-center">

        <div class="col-12 col-md-8 mb-2 mb-md-0">
          <h6 class="font-weight-bold mb-1">Elecciones 2023</h6>
          <small class="text-muted d-block">
            👥 60 participantes
          </small>
          <span class="badge badge-warning mt-1">
            Programada
          </span>
        </div>

        <div class="col-12 col-md-4 text-md-right">
          <a href="#" class="btn btn-outline-primary btn-sm mb-1">
            <i class="fas fa-edit"></i> Editar
          </a>
          <a href="#" class="btn btn-outline-danger btn-sm mb-1">
            <i class="fas fa-trash"></i> Eliminar
          </a>
        </div>

      </div>
    </div>
  </div>

</div>
@endsection
