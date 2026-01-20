{{-- resources/views/votante/elecciones/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Elecciones Disponibles - VOTAINCUBI')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-indigo-950 mb-2">Elecciones Disponibles</h1>
        <p class="text-gray-600">Consulta todas las elecciones y participa en las que están activas</p>
    </div>

    @if($elecciones->count() > 0)
        <!-- Grid de Elecciones -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($elecciones as $eleccion)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <!-- Header con estado -->
                <div class="h-32 relative overflow-hidden"
                     style="background: linear-gradient(135deg, 
                        {{ $eleccion->estadoEleccionesId == 2 ? '#10b981' : ($eleccion->estadoEleccionesId == 1 ? '#3b82f6' : '#6b7280') }} 0%, 
                        {{ $eleccion->estadoEleccionesId == 2 ? '#059669' : ($eleccion->estadoEleccionesId == 1 ? '#1d4ed8' : '#4b5563') }} 100%);">
                    
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    
                    <!-- Badge de Estado -->
                    <div class="absolute top-4 right-4">
                        @if($eleccion->estadoEleccionesId == 2)
                            <span class="bg-white text-green-600 px-3 py-1 rounded-full text-xs font-bold flex items-center">
                                <i class="fas fa-circle animate-pulse mr-1" style="font-size: 8px;"></i>
                                ABIERTA
                            </span>
                        @elseif($eleccion->estadoEleccionesId == 1)
                            <span class="bg-white text-blue-600 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-clock mr-1"></i>
                                PRÓXIMAMENTE
                            </span>
                        @else
                            <span class="bg-white text-gray-600 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check mr-1"></i>
                                CERRADA
                            </span>
                        @endif
                    </div>
                    
                    <!-- Icono central -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-vote-yea text-white text-5xl opacity-30"></i>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $eleccion->nombreEleccion }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $eleccion->descripcion }}</p>
                    
                    <!-- Información -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar-alt w-5 text-indigo-600"></i>
                            <span class="ml-2">
                                Inicio: {{ \Carbon\Carbon::parse($eleccion->fechaInicio)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar-xmark w-5 text-red-600"></i>
                            <span class="ml-2">
                                Cierre: {{ \Carbon\Carbon::parse($eleccion->fechaFin)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-users w-5 text-blue-600"></i>
                            <span class="ml-2">{{ $eleccion->candidatos->count() }} candidatos</span>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-2">
                        @if($eleccion->estadoEleccionesId == 2)
                            <a href="{{ route('votante.votar.lista', $eleccion->id) }}"
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded-lg font-semibold transition-colors duration-200">
                                <i class="fas fa-vote-yea mr-1"></i>
                                Votar Ahora
                            </a>
                        @endif
                        
                        <a href="{{ route('votante.elecciones.detalle', $eleccion->id) }}"
                           class="flex-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-center py-2 rounded-lg font-semibold transition-colors duration-200">
                            <i class="fas fa-info-circle mr-1"></i>
                            Ver Detalle
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="flex justify-center mt-8">
            {{ $elecciones->links() }}
        </div>
    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="max-w-md mx-auto">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">No hay elecciones disponibles</h3>
                <p class="text-gray-600 mb-6">Por el momento no existen elecciones programadas o activas.</p>
                <a href="{{ route('votante.home') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Inicio
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection