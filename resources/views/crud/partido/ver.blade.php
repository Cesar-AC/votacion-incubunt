@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Gestionar Partidos</h5>
    <a href="{{ route('crud.partido.crear') }}" class="btn btn-primary btn-sm shadow">
      Nuevo Partido
    </a>
  </div>

  <!-- Buscador (solo UI) -->
  <div class="mb-4">
    <input type="text" class="form-control" placeholder="Buscar partido">
  </div>

  @forelse ($partidos as $partido)

    <div class="card shadow-sm mb-3">
      <div class="card-body py-3">
        <div class="d-flex justify-content-between">

          <!-- Info -->
          <div>
            <h6 class="font-weight-bold mb-1">{{ $partido->partido }}</h6>
            <small class="text-muted d-block mb-2">
              {{ $partido->urlPartido ?? 'Sin URL' }}
            </small>
          </div>

          <!-- Acciones -->
          <div class="text-right">
            <a href="{{ route('crud.partido.editar', $partido->idPartido) }}"
               class="btn btn-outline-primary btn-sm mb-1">
              <i class="fas fa-edit"></i> Editar
            </a>

            <form method="POST"
                  action="{{ route('crud.partido.eliminar', $partido->idPartido) }}"
                  onsubmit="return confirm('¿Eliminar este partido?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-danger btn-sm">
                <i class="fas fa-trash"></i> Eliminar
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>

  @empty
    <div class="alert alert-info text-center">
      No hay partidos registrados aún.
    </div>
  @endforelse

</div>
@endsection
