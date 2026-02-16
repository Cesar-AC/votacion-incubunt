@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="font-weight-bold mb-0">Participantes del Padrón Electoral</h5>
    <a href="{{ route('crud.padron_electoral.ver') }}" class="btn btn-secondary btn-sm shadow">
      <i class="fas fa-arrow-left"></i> Volver
    </a>
  </div>

  <!-- Información de la elección -->
  <div class="card shadow-sm mb-4">
    <div class="card-body py-3">
      <h6 class="font-weight-bold mb-1">{{ $eleccion->titulo }}</h6>
      <div class="row">
        <div class="col-md-6">
          <small class="text-muted">
            <i class="fas fa-calendar"></i> 
            Inicio: {{ \Carbon\Carbon::parse($eleccion->fechaInicio)->format('d/m/Y H:i') }}
          </small>
        </div>
        <div class="col-md-6">
          <small class="text-muted">
            <i class="fas fa-calendar-check"></i> 
            Fin: {{ \Carbon\Carbon::parse($eleccion->fechaFin)->format('d/m/Y H:i') }}
          </small>
        </div>
      </div>
      <div class="mt-2">
        <span class="badge badge-primary">
          {{ $participantes->count() }} participante{{ $participantes->count() != 1 ? 's' : '' }}
        </span>
      </div>
    </div>
  </div>

  <!-- Lista de participantes -->
  @if($participantes->count() > 0)
  <div class="card shadow-sm">
    <div class="card-header bg-white py-3">
      <h6 class="mb-0 font-weight-bold">Lista de Participantes</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="thead-light">
            <tr>
              <th class="border-0">#</th>
              <th class="border-0">Nombre Completo</th>
              <th class="border-0">Correo Electrónico</th>
             
            </tr>
          </thead>
          <tbody>
            @foreach($participantes as $index => $usuario)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-secondary mr-2" 
                       style="width:32px; height:32px; flex-shrink: 0;">
                  </div>
                  <div>
                    <span class="font-weight-bold">
                      {{ $usuario->perfil->nombre ?? '' }}
                      {{ $usuario->perfil->apellidoPaterno ?? '' }}
                      {{ $usuario->perfil->apellidoMaterno ?? '' }}
                    </span>
                  </div>
                </div>
              </td>
              <td>{{ $usuario->correo }}</td>
            
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No hay participantes registrados en este padrón electoral.
  </div>
  @endif

</div>
@endsection
