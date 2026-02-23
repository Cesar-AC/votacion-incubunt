@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Cargo</h1>
        <a href="{{ route('crud.cargo.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="createCargoForm" action="{{ route('crud.cargo.crear') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="cargo">Nombre del Cargo</label>
                    <input type="text" class="form-control" id="cargo" name="cargo" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="idArea">Área</label>
                    <select class="form-control" id="idArea" name="idArea" required>
                        <option value="">Seleccione un área</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->idArea }}">{{ $area->area }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crear Cargo
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
