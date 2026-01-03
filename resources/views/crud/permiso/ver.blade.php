@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Permisos</h1>
        <a href="{{ route('crud.permiso.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Permiso
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Permiso</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permisos ?? [] as $permiso)
                            <tr>
                                <td>{{ $permiso->getKey() }}</td>
                                <td>{{ $permiso->permiso }}</td>
                                <td class="text-center">
                                    <a href="{{ route('crud.permiso.editar', $permiso->getKey()) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('crud.permiso.eliminar', $permiso->getKey()) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Desea eliminar este permiso?')">
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
                                    No hay permisos registrados
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
