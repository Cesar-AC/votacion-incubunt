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
  <div class="row grid grid-cols-3 lg:gap-3">
    
    <!-- Datos Personales -->
    <div class="col-span-3 lg:col-span-2">
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
    <div class="col-span-3 lg:col-span-1">
      <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
          <div class="text-center">
            <div class="flex justify-center items-center mb-3">
              <!-- Función soportada próximamente
              <label for="foto" class="hidden lg:block hover:opacity-100 opacity-0 transition-all duration-200 absolute w-100% z-20 bg-primary rounded-lg p-4 cursor-pointer">
                <i class="fas fa-camera text-white"></i>
                <p class="text-white">Cambiar foto</p>
                <input type="file" accept="image/*" class="hidden" id="foto">
              </label>
              -->

              <img src="{{ Auth::user()->perfil->obtenerFotoURL() ?? asset('img/undraw_profile.svg') }}" 
                  class="block img-fluid z-10 max-w-48 sm:max-w-96 lg:max-w-72 xl:max-w-100" 
                  alt="Foto de perfil">
            </div>
          </div>
          <h5 class="font-weight-bold mb-1">
            {{ $user->perfil->nombre ?? 'Usuario' }} 
            {{ $user->perfil->apellidoPaterno ?? '' }}
          </h5>
          
          <p class="text-muted">
            {{ $user->roles->first()->rol ?? 'Sin rol' }}
          </p>

          <hr class="my-3">

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

@push('scripts')

<script>
    document.getElementById('foto').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.querySelector('img');
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

@endpush
