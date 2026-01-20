@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="font-weight-bold mb-0">Mi Perfil</h5>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left"></i> Volver
    </a>
  </div>

  <!-- Información del Usuario -->
  <div class="row">
    
    <!-- Datos Personales -->
    <div class="col-lg-8">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h6 class="m-0 font-weight-bold">
            <i class="fas fa-user"></i> Datos Personales
          </h6>
        </div>
        <div class="card-body">
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Apellido Paterno</label>
              <p class="form-control-plaintext">{{ $user->perfil->apellidoPaterno ?? 'No registrado' }}</p>
            </div>
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Apellido Materno</label>
              <p class="form-control-plaintext">{{ $user->perfil->apellidoMaterno ?? 'No registrado' }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Nombre(s)</label>
              <p class="form-control-plaintext">{{ $user->perfil->nombre ?? 'No registrado' }}</p>
            </div>
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Otros Nombres</label>
              <p class="form-control-plaintext">{{ $user->perfil->otrosNombres ?? '-' }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">DNI</label>
              <p class="form-control-plaintext">{{ $user->perfil->dni ?? 'No registrado' }}</p>
            </div>
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Teléfono</label>
              <p class="form-control-plaintext">{{ $user->perfil->telefono ?? 'No registrado' }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Carrera</label>
              <p class="form-control-plaintext">{{ $user->perfil->carrera->carrera ?? 'No asignada' }}</p>
            </div>
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Área</label>
              <p class="form-control-plaintext">{{ $user->perfil->area->area ?? 'No asignada' }}</p>
            </div>
          </div>

        </div>
      </div>

      <!-- Información de la Cuenta -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
          <h6 class="m-0 font-weight-bold">
            <i class="fas fa-envelope"></i> Información de Cuenta
          </h6>
        </div>
        <div class="card-body">
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Correo Electrónico</label>
              <p class="form-control-plaintext">{{ $user->correo }}</p>
            </div>
            <div class="col-md-6">
              <label class="small text-muted font-weight-bold">Estado de Cuenta</label>
              <p class="form-control-plaintext">
                @if($user->estaActivo())
                  <span class="badge badge-success">Activo</span>
                @elseif($user->estaInactivo())
                  <span class="badge badge-secondary">Inactivo</span>
                @elseif($user->estaSuspendido())
                  <span class="badge badge-warning">Suspendido</span>
                @elseif($user->estaInhabilitado())
                  <span class="badge badge-danger">Inhabilitado</span>
                @else
                  <span class="badge badge-light">Desconocido</span>
                @endif
              </p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="small text-muted font-weight-bold">Rol(es) Asignado(s)</label>
              <p class="form-control-plaintext">
                @forelse($user->roles as $rol)
                  <span class="badge badge-primary mr-1">{{ $rol->rol }}</span>
                @empty
                  <span class="text-muted">Sin roles asignados</span>
                @endforelse
              </p>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Tarjeta de Perfil -->
    <div class="col-lg-4">
      <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
          <img src="{{ asset('img/undraw_profile.svg') }}" 
               class="img-fluid rounded-circle mb-3" 
               alt="Foto de perfil"
               style="max-width: 150px;">
          
          <h5 class="font-weight-bold mb-1">
            {{ $user->perfil->nombre ?? 'Usuario' }} 
            {{ $user->perfil->apellidoPaterno ?? '' }}
          </h5>
          
          <p class="text-muted mb-3">
            {{ $user->roles->first()->rol ?? 'Sin rol' }}
          </p>

          <hr>

          <div class="text-left">
            <p class="mb-2">
              <i class="fas fa-envelope text-primary mr-2"></i>
              <small>{{ $user->correo }}</small>
            </p>
            
            @if($user->perfil && $user->perfil->telefono)
            <p class="mb-2">
              <i class="fas fa-phone text-primary mr-2"></i>
              <small>{{ $user->perfil->telefono }}</small>
            </p>
            @endif

            @if($user->perfil && $user->perfil->dni)
            <p class="mb-2">
              <i class="fas fa-id-card text-primary mr-2"></i>
              <small>DNI: {{ $user->perfil->dni }}</small>
            </p>
            @endif
          </div>

        </div>
      </div>

      <!-- Información Adicional -->
      <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
          <h6 class="m-0 font-weight-bold">
            <i class="fas fa-info-circle"></i> Información
          </h6>
        </div>
        <div class="card-body">
          <p class="small text-muted mb-2">
            <i class="fas fa-user-shield mr-2"></i>
            ID de Usuario: <strong>{{ $user->idUser }}</strong>
          </p>
          
          <p class="small text-muted mb-2">
            <i class="fas fa-graduation-cap mr-2"></i>
            Carrera: <strong>{{ $user->perfil->carrera->carrera ?? 'No asignada' }}</strong>
          </p>

          <p class="small text-muted mb-0">
            <i class="fas fa-building mr-2"></i>
            Área: <strong>{{ $user->perfil->area->area ?? 'No asignada' }}</strong>
          </p>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
