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

  @include('components.error-message')

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
              📅 Inicio de votación: {{ \Carbon\Carbon::parse($e->fechaInicio)->format('d/m/Y') }} |
              Cierre de votación: {{ \Carbon\Carbon::parse($e->fechaCierre)->format('d/m/Y') }}
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
          @if ($e->estaProgramado())
          <div class="col-12 col-md-4 text-md-right">
            @if ($eleccionesService->votacionPosteriorAFechaCierre($e))
            <form action="{{ route('crud.elecciones.finalizar', $e->idElecciones) }}"
                  method="POST"
                  class="d-inline">
              @csrf
              <button class="btn btn-outline-success btn-sm mb-1"
                      onclick="return confirm('¿Finalizar esta elección?')">
                <i class="fas fa-check"></i> Finalizar
              </button>
            </form>
            @endif

            @if ($activaId !== $e->getKey())
            <form action="{{ route('crud.elecciones.activar', $e->idElecciones) }}"
                  method="POST"
                  class="d-inline">
              @csrf
              <button class="btn btn-outline-success btn-sm mb-1"
                      onclick="return confirm('¿Activar esta elección?')">
                <i class="fas fa-check"></i> Marcar como activa
              </button>
            </form>
            @endif

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
          @endif

          @if ($e->estaAnulado())
          <div class="col-12 col-md-4 text-md-right">
            <form action="{{ route('crud.elecciones.restaurar', $e->idElecciones) }}"
                  method="POST"
                  class="d-inline">
              @csrf
              <button class="btn btn-outline-success btn-sm mb-1"
                      onclick="return confirm('¿Restaurar esta elección?')">
                <i class="fas fa-check"></i> Restaurar
              </button>
            </form>
          </div>
          @endif

          @if ($e->estaFinalizado())
          <div class="col-12 col-md-4 text-md-right">
            <a href="{{ route('crud.voto.ver_resultados', $e->getKey()) }}"
               class="btn btn-outline-info btn-sm mb-1">
              <i class="fas fa-check"></i> Ver Resultados
            </a>
          </div>
          @endif

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

