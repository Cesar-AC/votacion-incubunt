@extends('layouts.admin')
@section('content')
<div class="container-fluid px-3">

  @include('components.error-message')

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

@foreach ($usuarios as $usuario)
<div class="card shadow-sm mb-3">
  <div class="card-body py-3">
    <div class="flex flex-col justify-content-between lg:flex-row lg:h-24">
      <div class="flex flex-col w-full text-center mb-2 sm:flex-row items-center lg:w-auto lg:h-full lg:mb-0">
        @if ($usuario->perfil?->tieneFoto())
        <div class="flex w-full justify-center sm:mr-4 lg:w-auto lg:h-full">
          <img src="{{ $usuario->perfil?->obtenerFotoURL() }}" class="w-full max-w-72 lg:w-auto lg:h-full my-2" alt="Foto de {{$usuario->perfil?->obtenerNombreApellido()}}">
        </div>
        @endif

        <div class="flex flex-col w-full justify-center gap-2 lg:justify-start lg:items-start">
          <h6 class="font-weight-bold">
              {{ $usuario->perfil?->obtenerNombreApellido() ?? $usuario->correo }}
          </h6>

          <small class="text-muted d-block">
            {{ $usuario->correo }}
          </small>

          @if($usuario->estaActivo())
          <span class="badge badge-primary">
            Rol:
            {{ $usuario->roles->first()->rol ?? 'Sin rol' }}
          </span>
          @else
          <span class="badge badge-danger">
            Deshabilitado
          </span>
          @endif

          @if($usuario->estaActivo())
          <div class="hidden sm:flex sm:flex-col sm:gap-1 lg:hidden">
            <a href="{{ route('crud.user.editar', $usuario->getKey()) }}" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('crud.user.permisos', $usuario->getKey()) }}" class="btn btn-outline-info btn-sm">
              <i class="fas fa-key"></i> Permisos
            </a>
            <a href="#" class="btn btn-outline-danger btn-sm" disabled>
              <i class="fas fa-trash"></i> Deshabilitar
            </a>
          </div>
          @endif
        </div>
      </div>

      @if($usuario->estaActivo())
      <div class="sm:hidden flex flex-col xl:text-right gap-1 lg:grid lg:grid-cols-2 lg:items-center">
        <a href="{{ route('crud.user.editar', $usuario->getKey()) }}" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('crud.user.permisos', $usuario->getKey()) }}" class="btn btn-outline-info btn-sm">
          <i class="fas fa-key"></i> Permisos
        </a>
        <form action="{{ route('crud.user.eliminar', $usuario->getKey()) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash"></i> Deshabilitar
          </button>
        </form>
      </div>
      @else 
      <form action="{{ route('crud.user.restaurar', $usuario->getKey()) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-success btn-sm">
          <i class="fas fa-check"></i> Restaurar
        </button>
      </form>
      @endif

    </div>
  </div>
</div>
@endforeach



</div>
@endsection