@extends('layouts.admin')

@section('title', 'Portal Votante - VOTAINCUBI')

@section('content')
<div class="container mx-auto px-4 py-6 pb-24">
    <!-- Bienvenida -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-indigo-950">¡Bienvenido votante!</h1>
        <p class="text-gray-600 mt-2">Aquí puedes participar en las elecciones activas</p>
    </div>

    <!-- Portal Votante Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-indigo-950 mb-2">Portal Votante</h2>
        <p class="text-gray-600">Participa de manera fácil y segura en los procesos electorales</p>
    </div>

    <!-- Elecciones Disponibles -->
    <section class="mb-8">
        <h3 class="text-xl font-bold text-indigo-950 mb-4">Elecciones Disponibles</h3>
        <p class="text-gray-600 mb-4">Puedes votar ahora en estas elecciones</p>

        @if(isset($eleccionActiva) && $eleccionActiva)
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="text-xl font-semibold text-indigo-950">{{ $eleccionActiva->nombreEleccion }}</h4>
                    <span class="inline-block bg-green-500 text-white text-xs px-3 py-1 rounded-full mt-2">Abierta</span>
                </div>
            </div>
            
            <div class="space-y-2 text-gray-600 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar-xmark mr-2"></i>
                    <span>Cierra el {{ \Carbon\Carbon::parse($eleccionActiva->fechaFin)->format('d/m/Y') }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    <span>{{ $eleccionActiva->candidatos->count() ?? 0 }} candidatos</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    <span>Inicio: {{ \Carbon\Carbon::parse($eleccionActiva->fechaInicio)->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('votante.votar.lista', ['eleccionId' => $eleccionActiva->id]) }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Votar Ahora
                </a>

                <a href="{{ route('votante.elecciones') }}"
                   class="bg-white hover:bg-gray-50 text-indigo-950 font-semibold px-6 py-2 rounded-lg border border-gray-300 transition">
                    Ver Elecciones
                </a>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-gray-300">
            <div class="text-center py-8">
                <i class="fas fa-vote-yea text-gray-400 text-5xl mb-4"></i>
                <h4 class="text-xl font-semibold text-gray-700 mb-2">No hay elecciones activas</h4>
                <p class="text-gray-600 mb-4">Por el momento no hay elecciones disponibles para votar</p>
                <a href="{{ route('votante.elecciones') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Ver Historial de Elecciones
                </a>
            </div>
        </div>
        @endif

        <div class="mt-4 text-right">
            <a href="{{ route('votante.elecciones') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                Ver Todas <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </section>

   <!-- Mis Participaciones -->
    <section>
        <h3 class="text-xl font-bold text-indigo-950 mb-4">Mis Participaciones</h3>

        @if(isset($participaciones) && $participaciones->count() > 0)
        <div class="bg-white rounded-lg shadow-md divide-y">
            @foreach($participaciones as $participacion)
            <div class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                <div>
                    <h5 class="font-semibold text-indigo-950">{{ $participacion->eleccion->nombreEleccion }}</h5>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($participacion->fechaVoto)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i>
                    Votado
                </span>
            </div>
            @endforeach
        </div>

        <!-- Paginación -->
        @if($participaciones->hasPages())
        <div class="flex justify-center items-center space-x-2 mt-6">
            @if($participaciones->onFirstPage())
                <button class="px-3 py-1 rounded text-gray-400" disabled>Anterior</button>
            @else
                <a href="{{ $participaciones->previousPageUrl() }}" 
                   class="px-3 py-1 rounded hover:bg-gray-100">Anterior</a>
            @endif

            @foreach(range(1, $participaciones->lastPage()) as $page)
                @if($page == $participaciones->currentPage())
                    <button class="px-3 py-1 rounded bg-indigo-600 text-white">{{ $page }}</button>
                @else
                    <a href="{{ $participaciones->url($page) }}" 
                       class="px-3 py-1 rounded hover:bg-gray-100">{{ $page }}</a>
                @endif
            @endforeach

            @if($participaciones->hasMorePages())
                <a href="{{ $participaciones->nextPageUrl() }}" 
                   class="px-3 py-1 rounded hover:bg-indigo-600 hover:text-white">Siguiente</a>
            @else
                <button class="px-3 py-1 rounded text-gray-400" disabled>Siguiente</button>
            @endif
        </div>
        @endif
        @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
            <h5 class="font-semibold text-gray-700 mb-2">No has participado en elecciones</h5>
            <p class="text-gray-600">Cuando votes en una elección, aparecerá aquí tu historial</p>
        </div>
        @endif
    </section>
</div>

<style>
/* Asegurar espacio suficiente para el bottom nav */
.container {
    padding-bottom: 100px !important;
}

/* Animaciones suaves para las tarjetas */
.hover\:bg-gray-50:hover {
    transition: background-color 0.2s ease;
}

/* Mejorar la interactividad de los botones */
.bg-green-600:hover,
.bg-indigo-600:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

/* Responsive para mobile */
@media (max-width: 640px) {
    .flex.space-x-3 {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .flex.space-x-3 a {
        width: 100%;
        text-align: center;
    }
}
</style>
@endsection