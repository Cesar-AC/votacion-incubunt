@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

<form method="POST" action="{{ route('crud.padron_electoral.editar', $eleccion->idElecciones) }}">
@csrf
@method('PUT')

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Editar Padrón Electoral</h5>
    <button type="submit" class="btn btn-primary btn-sm shadow">
      Actualizar padrón
    </button>
  </div>

  <!-- Elección (Read-only) -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="form-group mb-0">
        <label class="small font-weight-bold">Elección</label>
        <input type="text" class="form-control" value="{{ $eleccion->titulo }} ({{ \Carbon\Carbon::parse($eleccion->fechaInicio)->format('d/m/Y') }})" readonly>
      </div>
    </div>
  </div>

  <!-- Lista de usuarios -->
  <div class="card shadow-sm">
    <div class="card-body p-2">

      @foreach($usuarios as $u)
        <div class="d-flex align-items-center p-2 border-bottom">
          <input type="checkbox"
                 name="usuarios[]"
                 value="{{ $u->idUser }}"
                 class="mr-3"
                 {{ in_array($u->idUser, $padronUsuarios) ? 'checked' : '' }}>

          <div class="rounded-circle bg-secondary mr-3"
               style="width:40px; height:40px;"></div>

          <div class="flex-grow-1">
            <div class="font-weight-bold">
              {{ $u->perfil->nombre ?? '' }}
              {{ $u->perfil->apellidoPaterno ?? '' }}
              {{ $u->perfil->apellidoMaterno ?? '' }}
            </div>
            <small class="text-muted">
              {{ $u->correo }}
            </small>
          </div>
        </div>
      @endforeach

      @if($usuarios->isEmpty())
        <div class="text-center text-muted py-3">
          No hay usuarios disponibles
        </div>
      @endif

    </div>
  </div>

</form>
</div>
@endsection
