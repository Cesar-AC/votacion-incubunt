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
          <h6 class="font-weight-bold">Procesos Electorales</h6>
            <p class="text-muted small mb-3">
              {{$eleccionesTotal->count()}} proceso(s) electoral(es) programado(s)
            </p>
          <a href="{{ route('crud.elecciones.crear') }}" class="btn btn-primary btn-sm">
            Crear Proceso Electoral
          </a>
        </div>
      </div>
    </div>

    <!-- Padrones -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body text-center">
          <i class="fas fa-th-large fa-2x text-info mb-3"></i>
          <h6 class="font-weight-bold">Padrones Electorales</h6>
          <p class="text-muted small mb-3">
            {{ $stats['padrones'] ?? 0 }} usuarios en padrones
          </p>
          <a href="{{ route('crud.padron_electoral.crear') }}" class="btn btn-info btn-sm">
            Crear Padrón Electoral
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
            {{ $stats['usuarios'] ?? 0 }} usuarios registrados
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
            {{ $stats['admins'] ?? 0 }} administradores
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
        Seguimiento de la elección activa
      </h6>
    </div>

    <div class="card-body">

      @if(isset($eleccionActiva))
        <p class="mb-2"><strong>Elección:</strong> {{ $eleccionActiva->titulo }}</p>
        <p class="mb-4 text-muted"><strong>Estado actual:</strong>
          @if($eleccionesService->votacionHabilitada($eleccionActiva))
            En fecha de votación
          @elseif($eleccionesService->votacionPosteriorAFechaCierre($eleccionActiva))
            Votación finalizada
          @else
            La votación no ha comenzado
          @endif
        </p>

        @php
            $empadronamientoFinalizado = $eleccionActiva->padronElectoral->count() >= 1;
            $votacionHabilitada = $eleccionesService->votacionHabilitada($eleccionActiva);
            $votacionFinalizada = $eleccionesService->votacionPosteriorAFechaCierre($eleccionActiva);

            $fase2Finalizada = $empadronamientoFinalizado;
            $fase3Finalizada = $fase2Finalizada && ($votacionHabilitada || $votacionFinalizada);
            $fase4Finalizada = $fase3Finalizada && $votacionFinalizada;
        @endphp

        <!-- Barra de progreso -->
        <div class="progress mb-4" style="height: 20px;">
          <div class="progress-bar bg-primary" style="width: {{25 + ($fase2Finalizada ? 25 : 0) + ($fase3Finalizada ? 25 : 0) + ($fase4Finalizada ? 25 : 0)}}%;">
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
            <i class="@if($fase2Finalizada) fas fa-check-circle fa-2x text-success @else fas fa-spinner fa-spin fa-2x text-primary @endif"></i>
            <p class="mt-2 mb-0 font-weight-bold">Empadronamiento</p>
            @if ($fase2Finalizada)
              <small class="text-muted">Finalizado</small>
            @else
              <small class="text-muted">Pendiente</small>
            @endif
          </div>

          <div class="col-md-3">
            <i class="@if($votacionHabilitada) fas fa-spinner fa-spin fa-2x text-success @elseif ($votacionFinalizada) fas fa-check-circle fa-2x text-success @else fas fa-clock fa-2x text-secondary @endif"></i>
            <p class="mt-2 mb-0 font-weight-bold">Votación</p>
            @if ($votacionHabilitada)
              <small class="text-muted">En progreso. Cierra el {{ $eleccionActiva->fechaCierre->format('d-m-Y H:i') }}</small>
            @elseif ($votacionFinalizada)
              <small class="text-muted">La votación cerró el {{ $eleccionActiva->fechaCierre->format('d-m-Y H:i') }}</small>
            @else
              <small class="text-muted">Fuera de fecha ({{ $eleccionActiva->fechaInicio->format('d-m-Y H:i') }} - {{ $eleccionActiva->fechaCierre->format('d-m-Y H:i') }})</small>
            @endif
          </div>

          <div class="col-md-3">
            <i class="fas fa-clock fa-2x text-secondary"></i>
            <p class="mt-2 mb-0 font-weight-bold">Resultados</p>
            <small class="text-muted">Pendiente</small>
          </div>
        </div>
      @else
        <p class="mb-2"><strong>Elección:</strong> No hay una elección activa.</p>
        <p class="mb-4 text-muted"><strong>Estado actual:</strong> N/A</p>
        <div class="progress mb-4" style="height: 20px;">
          <div class="progress-bar bg-primary" style="width: 0%;">0% completado</div>
        </div>
      @endif
    </div>
  </div>

</div>

</div>
@endsection