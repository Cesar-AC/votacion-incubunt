@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Editar Mi Perfil</h5>
    <a href="{{ route('profile.show') }}" class="btn btn-secondary btn-sm">
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

  <form method="POST" action="{{ route('votante.perfil.actualizar') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h6 class="font-weight-bold mb-3">Datos Personales</h6>

        <input 
          name="apellidoPaterno" 
          class="form-control mb-2 @error('apellidoPaterno') is-invalid @enderror" 
          placeholder="Apellido Paterno" 
          value="{{ old('apellidoPaterno', $user->perfil->apellidoPaterno ?? '') }}"
          required>
        @error('apellidoPaterno')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input 
          name="apellidoMaterno" 
          class="form-control mb-2 @error('apellidoMaterno') is-invalid @enderror" 
          placeholder="Apellido Materno" 
          value="{{ old('apellidoMaterno', $user->perfil->apellidoMaterno ?? '') }}"
          required>
        @error('apellidoMaterno')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input 
          name="nombre" 
          class="form-control mb-2 @error('nombre') is-invalid @enderror" 
          placeholder="Nombre" 
          value="{{ old('nombre', $user->perfil->nombre ?? '') }}"
          required>
        @error('nombre')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input 
          name="otrosNombres" 
          class="form-control mb-2 @error('otrosNombres') is-invalid @enderror" 
          placeholder="Otros Nombres"
          value="{{ old('otrosNombres', $user->perfil->otrosNombres ?? '') }}">
        @error('otrosNombres')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input 
          name="dni" 
          class="form-control mb-2 @error('dni') is-invalid @enderror" 
          maxlength="8" 
          placeholder="DNI" 
          value="{{ old('dni', $user->perfil->dni ?? '') }}"
          required>
        @error('dni')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input 
          name="telefono" 
          class="form-control mb-2 @error('telefono') is-invalid @enderror" 
          placeholder="Telefono"
          value="{{ old('telefono', $user->perfil->telefono ?? '') }}">
        @error('telefono')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <select 
          name="idCarrera" 
          class="form-control mb-2 @error('idCarrera') is-invalid @enderror"
          required>
          <option value="">Seleccione carrera</option>
          @foreach($carreras as $carrera)
            <option value="{{ $carrera->idCarrera }}" {{ old('idCarrera', $user->perfil->idCarrera ?? '') == $carrera->idCarrera ? 'selected' : '' }}>
              {{ $carrera->carrera }}
            </option>
          @endforeach
        </select>
        @error('idCarrera')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <select 
          name="idArea" 
          class="form-control @error('idArea') is-invalid @enderror"
          required>
          <option value="">Seleccione area</option>
          @foreach($areas as $area)
            <option value="{{ $area->idArea }}" {{ old('idArea', $user->perfil->idArea ?? '') == $area->idArea ? 'selected' : '' }}>
              {{ $area->area }}
            </option>
          @endforeach
        </select>
        @error('idArea')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    @if($candidato)
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="font-weight-bold mb-3">Plan de Trabajo (Candidato)</h6>
          <input 
            name="planTrabajoCandidato" 
            class="form-control mb-2 @error('planTrabajoCandidato') is-invalid @enderror" 
            placeholder="URL del plan de trabajo"
            value="{{ old('planTrabajoCandidato', $candidato->planTrabajo ?? '') }}">
          @error('planTrabajoCandidato')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="text-muted">Ingresa un enlace publico a tu plan de trabajo.</small>
        </div>
      </div>
    @endif

    @if($partido)
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="font-weight-bold mb-3">Datos del Partido</h6>

          @if(!$puedeEditarPartido)
            <div class="alert alert-warning" role="alert">
              No tienes permiso para editar el partido.
            </div>
          @else
            <input 
              name="partido_urlPartido" 
              class="form-control mb-2 @error('partido_urlPartido') is-invalid @enderror" 
              placeholder="URL del partido"
              value="{{ old('partido_urlPartido', $partido->urlPartido ?? '') }}">
            @error('partido_urlPartido')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <textarea
              name="partido_descripcion"
              class="form-control mb-2 @error('partido_descripcion') is-invalid @enderror"
              rows="3"
              placeholder="Descripcion del partido">{{ old('partido_descripcion', $partido->descripcion ?? '') }}</textarea>
            @error('partido_descripcion')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <input 
              name="partido_planTrabajo" 
              class="form-control mb-2 @error('partido_planTrabajo') is-invalid @enderror" 
              placeholder="URL del plan de trabajo del partido"
              value="{{ old('partido_planTrabajo', $partido->planTrabajo ?? '') }}">
            @error('partido_planTrabajo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <input 
              name="partido_tipo" 
              class="form-control mb-2 @error('partido_tipo') is-invalid @enderror" 
              placeholder="Tipo"
              value="{{ old('partido_tipo', $partido->tipo ?? '') }}">
            @error('partido_tipo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <div class="mb-2">
              <label class="small text-muted font-weight-bold">Foto del partido</label>
              <input type="file" name="partido_foto" class="form-control-file @error('partido_foto') is-invalid @enderror">
              @error('partido_foto')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          @endif
        </div>
      </div>
    @endif

    <div class="text-right mb-4">
      <button type="submit" class="btn btn-primary px-4">
        Guardar Cambios
      </button>
    </div>

  </form>

</div>
@endsection
