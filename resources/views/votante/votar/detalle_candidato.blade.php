{{-- resources/views/votante/votar/detalle_candidato.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('votante.votar.lista', $eleccion->id) }}"
               class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Volver a la lista de candidatos
            </a>
        </div>

        {{-- Candidate Profile Card --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-6">
            
            {{-- Header with Party Colors --}}
            @if($candidato->partido)
            <div class="h-32 sm:h-40 relative"
                 style="background: linear-gradient(135deg, {{ $candidato->partido->color1 }} 0%, {{ $candidato->partido->color2 ?? $candidato->partido->color1 }} 100%);">
                <div class="absolute inset-0 bg-black opacity-20"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    @if($candidato->partido->logo)
                    <img src="{{ asset('storage/' . $candidato->partido->logo) }}" 
                         alt="{{ $candidato->partido->nombrePartido }}"
                         class="w-24 h-24 sm:w-32 sm:h-32 object-contain drop-shadow-2xl">
                    @endif
                </div>
            </div>
            @else
            <div class="h-32 sm:h-40 bg-gradient-to-r from-gray-700 to-gray-900 flex items-center justify-center">
                <span class="text-white text-2xl sm:text-3xl font-bold">CANDIDATO INDEPENDIENTE</span>
            </div>
            @endif

            {{-- Main Content --}}
            <div class="p-6 sm:p-8">
                
                {{-- Candidate Header --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6 mb-8">
                    @if($candidato->usuario && $candidato->usuario->perfil)
                    <img src="{{ $candidato->usuario->perfil->fotoPerfil ? asset('storage/' . $candidato->usuario->perfil->fotoPerfil) : asset('images/default-avatar.png') }}" 
                         alt="{{ $candidato->usuario->perfil->nombres }}"
                         class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover border-8 border-white shadow-2xl -mt-20 sm:-mt-24">
                    
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                                    {{ $candidato->usuario->perfil->nombres }} 
                                    {{ $candidato->usuario->perfil->apellidoPaterno }}
                                    {{ $candidato->usuario->perfil->apellidoMaterno }}
                                </h1>
                                
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                        {{ $candidato->cargo->nombreCargo }}
                                    </span>
                                    
                                    @if($candidato->partido)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold text-white"
                                          style="background-color: {{ $candidato->partido->color1 }};">
                                        {{ $candidato->partido->nombrePartido }}
                                    </span>
                                    @endif
                                </div>

                                @if($candidato->usuario->perfil->carrera)
                                <p class="text-gray-600 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                    {{ $candidato->usuario->perfil->carrera->nombreCarrera }}
                                </p>
                                @endif

                                @if($candidato->usuario->email)
                                <p class="text-gray-600 flex items-center mt-2">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    {{ $candidato->usuario->email }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Biography Section --}}
                @if($candidato->biografia)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="bg-blue-100 rounded-full p-2 mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        Biografía
                    </h2>
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $candidato->biografia }}</p>
                    </div>
                </div>
                @endif

                {{-- Proposals Section --}}
                @if($candidato->propuestas && $candidato->propuestas->isNotEmpty())
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="bg-green-100 rounded-full p-2 mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        Propuestas de Campaña
                    </h2>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($candidato->propuestas as $propuesta)
                        <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6 border border-blue-200 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        {{ $loop->iteration }}
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $propuesta->titulo }}</h3>
                                    <p class="text-gray-700 leading-relaxed">{{ $propuesta->descripcion }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Party Proposals (if applicable) --}}
                @if($candidato->partido && $candidato->partido->propuestas && $candidato->partido->propuestas->isNotEmpty())
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="bg-purple-100 rounded-full p-2 mr-3">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                            </svg>
                        </span>
                        Propuestas del Partido {{ $candidato->partido->nombrePartido }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($candidato->partido->propuestas as $propuesta)
                        <div class="bg-purple-50 rounded-xl p-5 border border-purple-200">
                            <h4 class="font-bold text-gray-900 mb-2">{{ $propuesta->titulo }}</h4>
                            <p class="text-sm text-gray-700">{{ $propuesta->descripcion }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Additional Info --}}
                @if($candidato->usuario && $candidato->usuario->perfil)
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Información Adicional</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($candidato->usuario->perfil->telefono)
                        <div class="flex items-center space-x-3">
                            <div class="bg-gray-100 rounded-full p-3">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Teléfono</p>
                                <p class="font-semibold text-gray-900">{{ $candidato->usuario->perfil->telefono }}</p>
                            </div>
                        </div>
                        @endif

                        @if($candidato->usuario->perfil->ciclo)
                        <div class="flex items-center space-x-3">
                            <div class="bg-gray-100 rounded-full p-3">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Ciclo Académico</p>
                                <p class="font-semibold text-gray-900">{{ $candidato->usuario->perfil->ciclo }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('votante.votar.lista', $eleccion->id) }}"
               class="bg-white text-gray-700 border-2 border-gray-300 py-4 rounded-xl font-semibold text-center hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                <span>Volver a votar</span>
            </a>
            <button onclick="window.print()"
                    class="bg-blue-600 text-white py-4 rounded-xl font-semibold text-center hover:bg-blue-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
                </svg>
                <span>Imprimir perfil</span>
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .max-w-5xl, .max-w-5xl * {
        visibility: visible;
    }
    .max-w-5xl {
        position: absolute;
        left: 0;
        top: 0;
    }
    button, a.no-print, .no-print {
        display: none !important;
    }
}
</style>
@endpush
@endsection