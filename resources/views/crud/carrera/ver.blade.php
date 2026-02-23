@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Carreras</h1>
        <a href="{{ route('crud.carrera.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Carrera
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Carrera</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carreras ?? [] as $carrera)
                            <tr>
                                <td>{{ $carrera->getKey() }}</td>
                                <td>{{ $carrera->carrera }}</td>
                                <td class="text-center">
                                    <a href="{{ route('crud.carrera.editar', $carrera->getKey()) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('crud.carrera.eliminar', $carrera->getKey()) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Desea eliminar esta carrera?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No hay carreras registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
