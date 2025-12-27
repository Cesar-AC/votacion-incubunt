@extends('layouts.admin')
@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="font-weight-bold mb-0">Gestionar Padrones</h5>
    <a href="{{ route('crud.padron_electoral.crear') }}" class="btn btn-primary btn-sm shadow">
      Nuevo Padrón
    </a>
  </div>

  <!-- Card Padron -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="d-flex justify-content-between">

        <div>
          <h6 class="font-weight-bold mb-1">Elecciones 2025</h6>
          <small class="text-muted">50 participantes</small>
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

  <!-- Card Padron -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="d-flex justify-content-between">

        <div>
          <h6 class="font-weight-bold mb-1">Elecciones 2024</h6>
          <small class="text-muted">50 participantes</small>
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

  <!-- Card Padron -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="d-flex justify-content-between">

        <div>
          <h6 class="font-weight-bold mb-1">Elecciones 2023</h6>
          <small class="text-muted">50 participantes</small>
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
<!-- Content Row -->