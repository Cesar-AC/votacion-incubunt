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

@foreach ($usuarios as $usuario)
<div class="card shadow-sm mb-3">
  <div class="card-body py-3">
    <div class="d-flex justify-content-between">

      <div>
        <h6 class="font-weight-bold mb-1">
          @if ($usuario->perfil)
            {{ $usuario->perfil->nombre }} {{ $usuario->perfil->apellidoPaterno }}
          @else
            {{ $usuario->correo }}
          @endif
        </h6>

        <small class="text-muted d-block mb-2">
          {{ $usuario->correo }}
        </small>

        <span class="badge badge-primary">
          Rol:
          {{ $usuario->roles->first()->rol ?? 'Sin rol' }}
        </span>
      </div>

      <div class="text-right">
        <a href="{{ route('crud.user.editar', $usuario->getKey()) }}" class="btn btn-outline-primary btn-sm mb-1">
          <i class="fas fa-edit"></i> Editar
        </a><br>
        <a href="#" class="btn btn-outline-danger btn-sm">
          <i class="fas fa-trash"></i> Eliminar
        </a>
        <a href="#" class="btn btn-outline-secondary btn-sm mt-1">
          <i class="fas fa-eye"></i> Ver
        </a>
      </div>

    </div>
  </div>
</div>
@endforeach



</div>
@endsection