@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Votos</h1>
        <a href="{{ route('crud.voto.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Voto
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Candidato</th>
                            <th>Fecha Voto</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($votos ?? [] as $voto)
                            <tr>
                                <td>{{ $voto->getKey() }}</td>
                                <td>{{ $voto->candidato->usuario->perfil ? $voto->candidato->usuario->perfil->nombre . ' ' . $voto->candidato->usuario->perfil->apellidoPaterno . ' ' . $voto->candidato->usuario->perfil->apellidoMaterno : 'N/A' }}</td>
                                <td>{{ $voto->fechaVoto ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('crud.voto.editar', $voto->getKey()) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('crud.voto.eliminar', $voto->getKey()) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Desea eliminar este voto?')">
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
                                <td colspan="4" class="text-center text-muted">
                                    No hay votos registrados
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
