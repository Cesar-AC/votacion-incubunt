@extends('layouts.admin')

@section('content')
<div class="row h-100">
                        <h1 class="h3 font-weight-bold mb-4 text-gray-800">Dashboard Administrador</h1>
<div class="container-fluid">

  <!-- Título -->
  <h4 class="mb-1 font-weight-bold text-gray-800">
    Acciones Principales del Sistema
  </h4>
  <p class="text-muted mb-4">
    Retroalimentación visual para diferentes estados de la aplicación y acciones rápidas
  </p>

  <!-- Cards -->
  <div class="row">

    <!-- Proceso Electoral -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body text-center">
          <i class="fas fa-th-large fa-2x text-primary mb-3"></i>
          <h6 class="font-weight-bold">Procesos Electorales Activos</h6>
          <p class="text-muted small mb-3">
            No hay votaciones disponibles en este momento
          </p>
          <a href="{{ route('crud.elecciones.crear') }}" class="btn btn-primary btn-sm">
            Crear Proceso Electoral
          </a>
        </div>
      </div>
    </div>

    <!-- Patrones -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body text-center">
          <i class="fas fa-th-large fa-2x text-info mb-3"></i>
          <h6 class="font-weight-bold">Patrones Electorales</h6>
          <p class="text-muted small mb-3">
            No hay padrones electorales disponibles
          </p>
          <a href="{{ route('crud.padron_electoral.crear') }}" class="btn btn-info btn-sm">
            Crear Patrón Electoral
          </a>
        </div>
      </div>
    </div>

    <!-- Usuarios -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body text-center">
          <i class="fas fa-users fa-2x text-warning mb-3"></i>
          <h6 class="font-weight-bold">Usuarios Registrados</h6>
          <p class="text-muted small mb-3">
            No hay usuarios disponibles
          </p>
          <a href="{{ route('crud.user.crear') }}" class="btn btn-warning btn-sm text-white">
            Crear Usuario
          </a>
        </div>
      </div>
    </div>

    <!-- Administradores -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body text-center">
          <i class="fas fa-user-shield fa-2x text-success mb-3"></i>
          <h6 class="font-weight-bold">Administradores Registrados</h6>
          <p class="text-muted small mb-3">
            Hay 2 administradores disponibles
          </p>
          <a href="{{ route('crud.user.crear') }}" class="btn btn-success btn-sm">
            Crear Administrador
          </a>
        </div>
      </div>
    </div>

  </div>
  <!-- Seguimiento -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        Seguimiento de Elección Más Reciente
      </h6>
    </div>

    <div class="card-body">

      <p class="mb-2"><strong>Elección:</strong> Consejo Universitario 2025</p>
      <p class="mb-4 text-muted"><strong>Estado actual:</strong> Jornada de votación en curso</p>

      <!-- Barra de progreso -->
      <div class="progress mb-4" style="height: 20px;">
        <div class="progress-bar bg-primary" style="width: 60%;">
          60% completado
        </div>
      </div>

      <!-- Estados -->
      <div class="row text-center">

        <div class="col-md-3">
          <i class="fas fa-check-circle fa-2x text-success"></i>
          <p class="mt-2 mb-0 font-weight-bold">Configuración</p>
          <small class="text-muted">Finalizada</small>
        </div>

        <div class="col-md-3">
          <i class="fas fa-check-circle fa-2x text-success"></i>
          <p class="mt-2 mb-0 font-weight-bold">Registro</p>
          <small class="text-muted">Finalizado</small>
        </div>

        <div class="col-md-3">
          <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
          <p class="mt-2 mb-0 font-weight-bold">Votación</p>
          <small class="text-muted">En progreso</small>
        </div>

        <div class="col-md-3">
          <i class="fas fa-clock fa-2x text-secondary"></i>
          <p class="mt-2 mb-0 font-weight-bold">Resultados</p>
          <small class="text-muted">Pendiente</small>
        </div>

      </div>

    </div>
  </div>

</div>

</div>
@endsection