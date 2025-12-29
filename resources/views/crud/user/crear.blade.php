@extends('layouts.admin')

@section('content')
<form method="POST" action="{{ route('crud.user.crear') }}">
@csrf

<div class="container-fluid px-3">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Nuevo Usuario</h5>
    <button type="submit" class="btn btn-primary btn-sm shadow">
      Guardar
    </button>
  </div>

  <!-- PERFIL -->
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h6 class="font-weight-bold mb-3">Perfil</h6>

      <input name="apellidoPaterno" class="form-control mb-2" placeholder="Apellido Paterno" required>
      <input name="apellidoMaterno" class="form-control mb-2" placeholder="Apellido Materno" required>
      <input name="nombre" class="form-control mb-2" placeholder="Nombre" required>
      <input name="otrosNombres" class="form-control mb-2" placeholder="Otros Nombres">

      <input name="dni" class="form-control mb-2" maxlength="8" placeholder="DNI" required>
      <input name="telefono" class="form-control mb-2" placeholder="Teléfono">

      <select name="idCarrera" class="form-control mb-2">
        <option value="">Seleccione carrera</option>
        <option value="1">Ingeniería de Sistemas</option>
        <option value="2">Administración</option>
      </select>

      <select name="idArea" class="form-control">
        <option value="">Seleccione área</option>
        <option value="1">GTH</option>
        <option value="2">RRHH</option>
      </select>
    </div>
  </div>

  <!-- CUENTA -->
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h6 class="font-weight-bold mb-3">Cuenta</h6>

      <input name="correo" type="email" class="form-control mb-2" placeholder="Correo" required>
      <input name="password" type="password" class="form-control mb-2" placeholder="Contraseña" required>

      <select name="idEstadoUsuario" class="form-control">
        <option value="1">Activo</option>
        <option value="2">Inactivo</option>
      </select>
    </div>
  </div>

  <!-- ROL -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h6 class="font-weight-bold mb-3">Rol</h6>

      <select name="idRol" class="form-control" required>
        <option value="">Seleccione rol</option>
        <option value="1">Administrador</option>
        <option value="2">Votante</option>
      </select>
    </div>
  </div>

</div>
</form>
@endsection
@section('scripts')
<script>
document.getElementById('formUser').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw data;
        return data;
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = "{{ route('crud.user.ver') }}";
        }
    })
    .catch(error => {
        if (error.errors) {
            let msg = '';
            Object.values(error.errors).forEach(e => msg += e[0] + '\n');
            alert(msg);
        } else {
            alert('Error inesperado');
        }
    });
});
</script>
@endsection
