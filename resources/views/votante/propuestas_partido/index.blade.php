@extends('layouts.admin')
@section('title', 'Mis Propuestas de Partido')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-flag-checkered"></i> Mis Propuestas de Partido
        </h1>
        @if(isset($partido))
        <a href="{{ route('votante.propuestas_partido.crear') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Propuesta
        </a>
        @endif
    </div>

    <!-- Mensajes -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(isset($mensaje))
    <div class="card shadow mb-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
            <h5 class="text-gray-700">{{ $mensaje }}</h5>
            <p class="text-muted">Para gestionar propuestas de partido, debes ser candidato afiliado a un partido político.</p>
        </div>
    </div>
    @else
    <!-- Información del Partido -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users"></i> {{ $partido->partido }}
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-0"><strong>Descripción:</strong> {{ $partido->descripcion ?? 'Sin descripción' }}</p>
            <p class="mb-0"><strong>Total de Propuestas:</strong> {{ $propuestas->count() }}</p>
        </div>
    </div>

    <!-- Lista de Propuestas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Lista de Propuestas
            </h6>
        </div>
        <div class="card-body">
            @if($propuestas->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-gray-500">No hay propuestas registradas para tu partido.</p>
                <a href="{{ route('votante.propuestas_partido.crear') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primera Propuesta
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Propuesta</th>
                            <th width="50%">Descripción</th>
                            <th width="20%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($propuestas as $propuesta)
                        <tr>
                            <td>{{ $propuesta->idPropuesta }}</td>
                            <td><strong>{{ $propuesta->propuesta }}</strong></td>
                            <td>{{ Str::limit($propuesta->descripcion, 100) }}</td>
                            <td class="text-center">
                                <a href="{{ route('votante.propuestas_partido.editar', $propuesta->idPropuesta) }}" 
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmarEliminar({{ $propuesta->idPropuesta }})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="form-eliminar-{{ $propuesta->idPropuesta }}" 
                                      action="{{ route('votante.propuestas_partido.eliminar', $propuesta->idPropuesta) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta propuesta? Esta acción no se puede deshacer.')) {
        document.getElementById('form-eliminar-' + id).submit();
    }
}
</script>
@endsection
