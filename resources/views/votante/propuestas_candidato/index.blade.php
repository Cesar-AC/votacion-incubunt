@extends('layouts.admin')

@section('title', 'Mis Propuestas como Candidato')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tie"></i> Mis Propuestas como Candidato
        </h1>
        @if(isset($candidato) && isset($elecciones) && $elecciones->isNotEmpty())
        <a href="{{ route('votante.propuestas_candidato.crear') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
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
            <p class="text-muted">Para gestionar propuestas como candidato, debes estar registrado y participando en al menos una elección.</p>
        </div>
    </div>
    @else
    <!-- Información del Candidato -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-user-circle"></i> Información del Candidato
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Nombre:</strong> {{ $candidato->usuario?->perfil?->nombre }} {{ $candidato->usuario?->perfil?->apellidoPaterno }}</p>
                    <p class="mb-2"><strong>Cargo:</strong> {{ $candidato->cargo->cargo ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Partido:</strong> {{ $candidato->partido->partido ?? 'Independiente' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Total de Propuestas:</strong> {{ $propuestas->count() }}</p>
                    <p class="mb-0"><strong>Elecciones en las que participa:</strong> {{ $elecciones->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Elecciones en las que participa -->
    @if(isset($elecciones) && $elecciones->isNotEmpty())
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-vote-yea"></i> Elecciones en las que Participas
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($elecciones as $eleccion)
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3">
                        <h6 class="font-weight-bold text-primary">{{ $eleccion->titulo }}</h6>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-calendar"></i> 
                            {{ $eleccion->fechaInicio->format('d/m/Y') }} - {{ $eleccion->fechaCierre->format('d/m/Y') }}
                        </p>
                        <span class="badge badge-{{ $eleccion->estado->idEstado == 1 ? 'success' : 'secondary' }}">
                            {{ $eleccion->estado->estado }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Lista de Propuestas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Mis Propuestas
            </h6>
        </div>
        <div class="card-body">
            @if($propuestas->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-gray-500">No tienes propuestas registradas.</p>
                @if(isset($elecciones) && $elecciones->isNotEmpty())
                <a href="{{ route('votante.propuestas_candidato.crear') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primera Propuesta
                </a>
                @endif
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
                                <a href="{{ route('votante.propuestas_candidato.editar', $propuesta->idPropuesta) }}" 
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmarEliminar({{ $propuesta->idPropuesta }})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="form-eliminar-{{ $propuesta->idPropuesta }}" 
                                      action="{{ route('votante.propuestas_candidato.eliminar', $propuesta->idPropuesta) }}" 
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
