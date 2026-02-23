@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Carrera</h1>
        <a href="{{ route('crud.carrera.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="createCarreraForm" action="{{ route('crud.carrera.crear') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="carrera">Nombre de la Carrera</label>
                    <input type="text" class="form-control" id="carrera" name="carrera" required maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crear Carrera
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
