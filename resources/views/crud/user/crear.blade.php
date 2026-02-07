@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Nuevo Usuario</h5>
  </div>

  @include('components.error-message')

  <form method="POST" action="{{ route('crud.user.crear') }}" enctype="multipart/form-data">
    @csrf

    <!-- PERFIL -->
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h6 class="font-weight-bold mb-3">Perfil</h6>

        <input
          name="apellidoPaterno"
          class="form-control mb-2 @error('apellidoPaterno') is-invalid @enderror"
          placeholder="Apellido Paterno"
          value="{{ old('apellidoPaterno') }}"
          required>
        @error('apellidoPaterno')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input
          name="apellidoMaterno"
          class="form-control mb-2 @error('apellidoMaterno') is-invalid @enderror"
          placeholder="Apellido Materno"
          value="{{ old('apellidoMaterno') }}"
          required>
        @error('apellidoMaterno')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input
          name="nombre"
          class="form-control mb-2 @error('nombre') is-invalid @enderror"
          placeholder="Nombre"
          value="{{ old('nombre') }}"
          required>
        @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input
          name="otrosNombres"
          class="form-control mb-2 @error('otrosNombres') is-invalid @enderror"
          placeholder="Otros Nombres"
          value="{{ old('otrosNombres') }}">
        @error('otrosNombres')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input
          name="dni"
          class="form-control mb-2 @error('dni') is-invalid @enderror"
          maxlength="8"
          placeholder="DNI"
          value="{{ old('dni') }}"
          required>
        @error('dni')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input
          name="telefono"
          class="form-control mb-2 @error('telefono') is-invalid @enderror"
          placeholder="Teléfono"
          value="{{ old('telefono') }}">
        @error('telefono')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <select
          name="idCarrera"
          class="form-control mb-2 @error('idCarrera') is-invalid @enderror">
          <option value="">Seleccione carrera</option>
          @foreach($carreras as $carrera)
          <option value="{{ $carrera->idCarrera }}" {{ old('idCarrera') == $carrera->idCarrera ? 'selected' : '' }}>
            {{ $carrera->carrera }}
          </option>
          @endforeach
        </select>
        @error('idCarrera')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <select
          name="idArea"
          class="form-control @error('idArea') is-invalid @enderror">
          <option value="">Seleccione área</option>
          @foreach($areas as $area)
          <option value="{{ $area->idArea }}" {{ old('idArea') == $area->idArea ? 'selected' : '' }}>
            {{ $area->area }}
          </option>
          @endforeach
        </select>
        @error('idArea')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- CUENTA -->
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h6 class="font-weight-bold mb-3">Cuenta</h6>

        <input
          name="correo"
          type="email"
          class="form-control mb-2 @error('correo') is-invalid @enderror"
          placeholder="Correo"
          value="{{ old('correo') }}"
          required>
        @error('correo')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <input
          name="contraseña"
          type="password"
          class="form-control mb-2 @error('contraseña') is-invalid @enderror"
          placeholder="Contraseña"
          required>
        @error('contraseña')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <select
          name="idEstadoUsuario"
          class="form-control @error('idEstadoUsuario') is-invalid @enderror"
          required>
          <option value="1" {{ old('idEstadoUsuario') == 1 ? 'selected' : '' }}>Activo</option>
          <option value="2" {{ old('idEstadoUsuario') == 2 ? 'selected' : '' }}>Inactivo</option>
        </select>
        @error('idEstadoUsuario')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- ROL -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h6 class="font-weight-bold mb-3">Rol</h6>

        <select
          name="idRol"
          class="form-control @error('idRol') is-invalid @enderror"
          required>
          <option value="">Seleccione rol</option>
          <option value="1" {{ old('idRol') == 1 ? 'selected' : '' }}>Administrador</option>
          <option value="2" {{ old('idRol') == 2 ? 'selected' : '' }}>Votante</option>
        </select>
        @error('idRol')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- FOTO -->
    <div class="card shadow-sm mb-4" x-data="{inputFoto: null}">
      <div class="card-body">
        <h6 class="font-weight-bold mb-3">Foto</h6>

        <div class="form-group">
          <input
            type="file"
            x-ref="inputFoto"
            name="foto"
            id="inputFoto"
            class="form-control-file @error('foto') is-invalid @enderror"
            accept=".png, .jpg, .jpeg, .gif"
            x-model="inputFoto"
            value="{{ old('foto') }}">
          @error('foto')
          <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div id="fotoPreview" class="mt-3" x-cloak x-show="inputFoto != null">
          <p class="text-muted mb-2">Previsualización:</p>
          <img id="visualizacionFoto" src="" alt="Previsualización de foto" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
        </div>
      </div>
    </div>

    <div class="text-right mb-4">
      <a href="{{ route('crud.user.ver') }}" class="btn btn-secondary px-4 mr-2">
        Volver
      </a>
      <button type="submit" class="btn btn-primary px-4">
        Guardar Usuario
      </button>
    </div>

  </form>

</div>
@endsection

@push('scripts')
@include('components.preview-upload-photo-script')
@endpush