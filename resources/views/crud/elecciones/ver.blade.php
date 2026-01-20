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

  @php
    $activaId = $eleccionActiva?->getKey();
  @endphp

  @forelse($elecciones as $e)
    <div class="card shadow-sm mb-3">
      <div class="card-body py-3">
        <div class="row align-items-center">

          <!-- Info -->
          <div class="col-12 col-md-8 mb-2 mb-md-0">
            <h6 class="font-weight-bold mb-1">
              {{ $e->titulo }}
            </h6>

            <small class="text-muted d-block">
              📅 Inicio: {{ \Carbon\Carbon::parse($e->fechaInicio)->format('d/m/Y') }} |
              Cierre: {{ \Carbon\Carbon::parse($e->fechaCierre)->format('d/m/Y') }}
            </small>

            <small class="text-muted d-block">
              👥 {{ $e->usuarios_count }} participantes
              @if($e->usuarios_count === 0)
                — <span class="text-danger">Sin padrón asignado</span>
              @endif
            </small>

            {{-- Estado --}}
            @php
              $badge = match(true) {
                $e->estaActivo() => 'success',
                $e->estaProgramado() => 'warning',
                $e->estaFinalizado() => 'secondary',
                $e->estaAnulado() => 'danger',
                default => 'light'
              };
            @endphp

            <span class="badge badge-{{ $badge }} mt-1">
              {{ $e->estadoEleccion->estado ?? 'Sin estado' }}
            </span>
            @if($activaId === $e->idElecciones)
              <span class="badge badge-info mt-1">Activa</span>
            @endif
          </div>

          <!-- Acciones -->
          <div class="col-12 col-md-4 text-md-right">
            <a href="{{ route('crud.elecciones.editar', $e->idElecciones) }}"
               class="btn btn-outline-primary btn-sm mb-1">
              <i class="fas fa-edit"></i> Editar
            </a>

            <form action="{{ route('crud.elecciones.eliminar', $e->idElecciones) }}"
                  method="POST"
                  class="d-inline">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-danger btn-sm mb-1"
                      onclick="return confirm('¿Eliminar esta elección?')">
                <i class="fas fa-trash"></i> Eliminar
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>
  @empty
    <div class="alert alert-info">
      No hay elecciones registradas.
    </div>
  @endforelse

</div>
@endsection

