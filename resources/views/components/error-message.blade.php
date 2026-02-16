<!-- Mensajes de éxito -->
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong><i class="fas fa-check-circle"></i> Éxito:</strong> {{ session('success') }}
    
    @if (session('candidatos_exitosos'))
    <ul class="mb-0 mt-2">
        @foreach (session('candidatos_exitosos') as $candidato)
        <li>{{ $candidato }}</li>
        @endforeach
    </ul>
    @endif
    
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Mensajes de advertencia -->
@if (session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong><i class="fas fa-exclamation-triangle"></i> Advertencia:</strong> {{ session('warning') }}
    
    @if (session('candidatos_fallidos'))
    <ul class="mb-0 mt-2">
        @foreach (session('candidatos_fallidos') as $fallido)
        <li>
            <strong>{{ $fallido['nombre'] }}:</strong> {{ $fallido['error'] }}
        </li>
        @endforeach
    </ul>
    @endif
    
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Errores de validación -->
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fas fa-times-circle"></i> Error:</strong>
    
    @if ($errors->has('error_general'))
        <p class="mb-2">{{ $errors->first('error_general') }}</p>
    @endif
    
    @if ($errors->has('detalles'))
        <p class="mb-1"><strong>Detalles de los errores:</strong></p>
        <ul class="mb-0">
            @foreach ($errors->get('detalles')[0] as $detalle)
            <li>
                <strong>{{ $detalle['nombre'] }}:</strong> {{ $detalle['error'] }}
            </li>
            @endforeach
        </ul>
    @elseif ($errors->count() > 0)
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                @if ($error !== $errors->first('error_general'))
                <li>{{ $error }}</li>
                @endif
            @endforeach
        </ul>
    @endif
    
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif