@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="font-weight-bold mb-0">Gestionar Padrones</h5>
    <a href="{{ route('crud.padron_electoral.crear') }}" class="btn btn-primary btn-sm shadow">
      Nuevo Padrón
    </a>
  </div>

  @forelse ($elecciones as $eleccion)
    <div class="card shadow-sm mb-3">
      <div class="card-body py-3">
        <div class="d-flex justify-content-between">

          <div>
            <h6 class="font-weight-bold mb-1">
              {{ $eleccion->titulo }}
            </h6>
            <small class="text-muted">
              {{ $eleccion->participantes_count }} participantes
            </small>
          </div>

          <div class="text-right">
            <a href="{{ route('crud.padron_electoral.editar', $eleccion->idElecciones) }}"
               class="btn btn-outline-primary btn-sm mb-1">
              <i class="fas fa-edit"></i> Editar
            </a><br>

            <form method="POST"
                  action="{{ route('crud.padron_electoral.eliminar', $eleccion->idElecciones) }}"
                  class="d-inline">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-danger btn-sm"
                      onclick="return confirm('¿Eliminar padrón?')">
                <i class="fas fa-trash"></i> Eliminar
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>
  @empty
    <div class="alert alert-info">
      No existen padrones creados aún.
    </div>
  @endforelse

</div>
@endsection
