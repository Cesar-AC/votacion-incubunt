@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">

<form method="POST" action="{{ route('crud.padron_electoral.importar_archivo') }}" enctype="multipart/form-data">
@csrf

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0">Importar Padrón Electoral</h5>
    <button type="submit" class="btn btn-primary btn-sm shadow">
      Importar archivo
    </button>
  </div>

  <!-- Elección -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="form-group mb-0">
        <label class="small font-weight-bold">Elección (Programada)</label>
        <select name="idElecciones" class="form-control" required>
          <option value="">-- Seleccione una elección --</option>
          @foreach($elecciones as $e)
            <option value="{{ $e->idElecciones }}">
              {{ $e->titulo }} ({{ \Carbon\Carbon::parse($e->fechaInicio)->format('d/m/Y') }})
            </option>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  <!-- Archivo -->
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <div class="form-group mb-0">
        <label class="small font-weight-bold">Archivo (CSV o Excel)</label>
        <input type="file" name="archivo" class="form-control" accept=".csv,.xlsx,.xls" required>
        <small class="form-text text-muted">
          Formatos soportados: CSV, XLSX, XLS. Tamaño máximo: 10MB.
        </small>
      </div>
    </div>
  </div>

  <!-- Información -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h6>Instrucciones para el archivo:</h6>
      <ul>
        <li>El archivo debe contener las columnas en este orden: correo, área (opcional), nombres, apellidos, dni, teléfono</li>
        <li>Para CSV: la primera fila debe ser los encabezados</li>
        <li>Para Excel: los encabezados deben estar en la primera fila</li>
        <li>Los usuarios serán creados si no existen, o asociados si ya existen</li>
        <li>Los registros duplicados (por correo o DNI) serán omitidos</li>
        <li>Si ocurre un error en un registro, será omitido con el motivo</li>
      </ul>
    </div>
  </div>

</form>

  <!-- Volver -->
  <div class="mt-3">
    <a href="{{ route('crud.padron_electoral.ver') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Volver
    </a>
  </div>

</div>
@endsection
