@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Área</h1>
        <a href="{{ route('crud.area.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @include('components.error-message')

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="editAreaForm" action="{{ route('crud.area.editar', $area->getKey()) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="area">Nombre del Área</label>
                    <input type="text" class="form-control" id="area" name="area" value="{{ $area->area }}" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="siglas">Siglas del Área</label>
                    <input type="text" class="form-control" id="siglas" name="siglas" value="{{ $area->siglas }}" required maxlength="10">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Área
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
