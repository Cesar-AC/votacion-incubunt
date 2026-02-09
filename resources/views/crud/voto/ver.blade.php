@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestión de Votos</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @include('components.error-message')
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Entidad</th>
                            <th>Elección</th>
                            <th>Tipo de Voto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($votos ?? [] as $voto)
                            <tr>
                                <td>{{ $voto['id'] }}</td>
                                <td>
                                    <span class="badge @if($voto['tipo'] === 'candidato') badge-info @else badge-success @endif">
                                        {{ ucfirst($voto['tipo']) }}
                                    </span>
                                </td>
                                <td>{{ $voto['entidad'] }}</td>
                                <td>{{ $voto['eleccion'] }}</td>
                                <td>{{ $voto['tipoVoto'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
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
