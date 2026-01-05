@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Candidatos</h1>
        <a href="{{ route('crud.candidato.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Candidato
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Partido</th>
                            <th>Cargo</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidatos ?? [] as $candidato)
                            <tr>
                                <td>{{ $candidato->getKey() }}</td>
                                <td>{{ $candidato->usuario->perfil ? $candidato->usuario->perfil->nombre . ' ' . $candidato->usuario->perfil->apellidoPaterno . ' ' . $candidato->usuario->perfil->apellidoMaterno : 'N/A' }}</td>
                                <td>{{ $candidato->partido->partido ?? 'N/A' }}</td>
                                <td>{{ $candidato->cargo->cargo ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('crud.candidato.editar', $candidato->getKey()) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('crud.candidato.eliminar', $candidato->getKey()) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Desea eliminar este candidato?')">
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
                                <td colspan="5" class="text-center text-muted">
                                    No hay candidatos registrados
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
