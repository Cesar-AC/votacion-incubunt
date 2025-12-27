@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Nuevo Usuario</h5>
    <button class="btn btn-primary btn-sm shadow">
      Guardar
    </button>
  </div>

  <!-- PERFIL -->
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h6 class="font-weight-bold mb-3">Perfil</h6>

      <div class="form-group">
        <label class="small font-weight-bold">Apellido Paterno</label>
        <input type="text" class="form-control">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Apellido Materno</label>
        <input type="text" class="form-control">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Nombre</label>
        <input type="text" class="form-control">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Otros Nombres</label>
        <input type="text" class="form-control">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">DNI</label>
        <input type="text" class="form-control" maxlength="8">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Teléfono</label>
        <input type="text" class="form-control">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Carrera</label>
        <select class="form-control">
          <option>Seleccione carrera</option>
          <option>Ingeniería de Sistemas</option>
          <option>Administración</option>
        </select>
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Área</label>
        <select class="form-control">
          <option>Seleccione área</option>
          <option>GTH</option>
          <option>RRHH</option>
        </select>
      </div>
    </div>
  </div>

  <!-- CUENTA -->
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h6 class="font-weight-bold mb-3">Cuenta</h6>

      <div class="form-group">
        <label class="small font-weight-bold">Correo</label>
        <input type="email" class="form-control">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Contraseña</label>
        <input type="password" class="form-control">
      </div>

      <div class="form-group">
        <label class="small font-weight-bold">Estado del Usuario</label>
        <select class="form-control">
          <option>Activo</option>
          <option>Inactivo</option>
        </select>
      </div>
    </div>
  </div>

  <!-- PERMISOS Y ROL -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h6 class="font-weight-bold mb-3">Permisos y Rol</h6>

      <div class="form-group">
        <label class="small font-weight-bold">Rol</label>
        <select class="form-control">
          <option>Seleccione rol</option>
          <option>Administrador</option>
          <option>Moderador</option>
          <option>Votante</option>
        </select>
      </div>

      <div class="form-group mb-0">
        <label class="small font-weight-bold">Permiso</label>
        <select class="form-control">
          <option>Seleccione permiso</option>
          <option>Acceso total</option>
          <option>Solo lectura</option>
          <option>Gestión de elecciones</option>
        </select>
      </div>

    </div>
  </div>

</div>
@endsection
