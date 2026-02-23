@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="font-weight-bold mb-0">Permisos de Usuario</h5>
      <small class="text-muted">{{ $usuario->correo }} — {{ $usuario->perfil->nombre ?? 'Sin perfil' }}</small>
    </div>
    <a href="{{ route('crud.user.ver') }}" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left"></i> Volver
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <div class="row">
    <!-- Asignar permiso -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h6 class="m-0 font-weight-bold">Asignar permiso</h6>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('crud.user.permisos.asignar', $usuario->getKey()) }}">
            @csrf
            <div class="form-group">
              <label class="small font-weight-bold">Permiso</label>
              <select name="permiso_id" class="form-control @error('permiso_id') is-invalid @enderror" required>
                <option value="">Seleccione permiso</option>
                @foreach($permisos as $permiso)
                  <option value="{{ $permiso->idPermiso }}">{{ $permiso->permiso }}</option>
                @endforeach
              </select>
              @error('permiso_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-primary">Asignar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Permisos actuales -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
          <h6 class="m-0 font-weight-bold">Permisos asignados</h6>
        </div>
        <div class="card-body p-0">
          @if($usuario->permisos->isEmpty())
            <div class="p-3 text-muted">Sin permisos asignados.</div>
          @else
            <div class="list-group list-group-flush">
              @foreach($usuario->permisos as $permiso)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <span>{{ $permiso->permiso }}</span>
                  <form method="POST" action="{{ route('crud.user.permisos.quitar', [$usuario->getKey(), $permiso->idPermiso]) }}" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Quitar este permiso?')">
                      <i class="fas fa-times"></i>
                    </button>
                  </form>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
