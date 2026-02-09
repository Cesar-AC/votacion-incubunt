{{-- resources/views/votante/votar/exito.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        
        {{-- Success Animation --}}
        <div class="text-center mb-8 animate-bounce-in">
            <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 bg-green-600 rounded-full shadow-2xl mb-6">
                <svg class="w-12 h-12 sm:w-14 sm:h-14 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-3">
                ¡Voto Confirmado!
            </h1>
            <p class="text-base sm:text-lg text-gray-600">
                Tu voto ha sido registrado exitosamente
            </p>
        </div>

        {{-- Vote Summary Card --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-6">
            
            {{-- Election Info --}}
            <div class="p-6 sm:p-8 bg-gradient-to-r from-blue-700 to-blue-900">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-full p-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-white">{{ $eleccion->nombreEleccion }}</h2>
                        <p class="text-white text-opacity-90 mt-1">{{ $eleccion->descripcion }}</p>
                    </div>
                </div>
            </div>

            {{-- Votes Cast --}}
            <div class="p-6 sm:p-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 rounded-full p-2 mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </span>
                    Tus Votos Registrados
                </h3>


                <div class="space-y-4">
                    {{-- Voto a Partido --}}
                    @foreach($votosPartido as $voto)
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-5 border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">
                                    Partido
                                </p>
                                <p class="font-bold text-lg text-blue-900">
                                    {{ $voto->partido->partido ?? 'Partido' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    {{-- Votos a Candidatos --}}
                    @foreach($votosCandidato as $voto)
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-5 border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                @if($voto->candidato && $voto->candidato->cargo)
                                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">
                                    {{ $voto->candidato->cargo->nombreCargo ?? 'Sin cargo' }}
                                </p>
                                @else
                                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">
                                    Cargo desconocido
                                </p>
                                @endif
                            </div>
                            <span class="bg-green-100 text-green-900 text-xs font-bold px-3 py-1 rounded-full border border-green-600">
                                Registrado
                            </span>
                        </div>

                        <div class="flex items-center space-x-4">
                            @if($voto->candidato && $voto->candidato->usuario && $voto->candidato->usuario->perfil)
                            <img src="{{ $voto->candidato->usuario->perfil->fotoPerfil ? asset('storage/' . $voto->candidato->usuario->perfil->fotoPerfil) : asset('images/default-avatar.png') }}" 
                                 alt="{{ $voto->candidato->usuario->perfil->nombres }}"
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-white shadow-lg">
                            
                            <div class="flex-1">
                                <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">
                                    {{ $voto->candidato->usuario->perfil->nombres ?? 'Sin nombre' }} 
                                    {{ $voto->candidato->usuario->perfil->apellidoPaterno ?? '' }}
                                </h4>
                                
                                @if($voto->candidato->partido)
                                <div class="flex items-center space-x-2 mb-2">
                                    @if($voto->candidato->partido->logo)
                                    <img src="{{ asset('storage/' . $voto->candidato->partido->logo) }}" 
                                         alt="{{ $voto->candidato->partido->nombrePartido }}"
                                         class="w-6 h-6 object-contain">
                                    @endif
                                    <p class="text-sm font-medium text-gray-700">
                                        {{ $voto->candidato->partido->nombrePartido }}
                                    </p>
                                </div>
                                @else
                                <p class="text-sm font-medium text-gray-600 mb-2">Candidato Independiente</p>
                                @endif

                                @if($voto->candidato->usuario->perfil->carrera)
                                <p class="text-xs text-gray-600">
                                    {{ $voto->candidato->usuario->perfil->carrera->nombreCarrera }}
                                </p>
                                @endif
                            </div>
                            @else
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white shadow-lg">
                                <i class="fas fa-user text-gray-400 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">
                                    Candidato ID: {{ $voto->idCandidato }}
                                </h4>
                                <p class="text-sm font-medium text-gray-600">Información no disponible</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 text-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        <p class="text-yellow-800 font-medium">No se registraron votos. Intenta nuevamente.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Vote Info --}}
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-wrap items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center space-x-2 mb-2 sm:mb-0">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Fecha:</span>
                        <span>{{ $votos->isNotEmpty() ? $votos->first()->fechaVoto->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Total de votos:</span>
                        <span class="font-bold text-blue-600">{{ $votos->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Important Information --}}
        <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-4 sm:p-6 mb-6 shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm sm:text-base font-bold text-blue-900 mb-2">
                        Información importante
                    </h3>
                    <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                        <li>Tu voto ha sido registrado de forma segura y anónima</li>
                        <li>No es posible modificar o cambiar tu voto una vez registrado</li>
                        <li>Los resultados estarán disponibles una vez finalice el proceso electoral</li>
                        <li>Puedes imprimir este comprobante para tus registros</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Redirection Info --}}
        <div class="bg-green-50 border-l-4 border-green-600 rounded-lg p-4 sm:p-6 mb-6 shadow-md text-center">
            <p class="text-base text-green-800 font-semibold mb-3">
                Serás redirigido al inicio automáticamente
            </p>
            <div class="flex items-center justify-center space-x-2">
                <span class="text-gray-700 text-sm">Redirigiendo en</span>
                <span id="countdown" class="font-bold text-green-600 text-2xl">5</span>
                <span class="text-gray-700 text-sm">segundos...</span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <a href="{{ route('votante.elecciones') }}"
               class="bg-white text-gray-800 border-2 border-gray-400 py-4 rounded-xl font-bold text-center hover:bg-gray-100 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center space-x-2 focus:outline-none focus:ring-4 focus:ring-gray-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                <span>Ver Elecciones</span>
            </a>
                <a href="{{ route('votante.elecciones') }}"
                    class="bg-blue-700 text-white py-4 rounded-xl font-bold text-center hover:bg-blue-800 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center space-x-2 focus:outline-none focus:ring-4 focus:ring-blue-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span>Ir al Inicio</span>
            </a>
        </div>

        {{-- Download Certificate Button --}}
        <div class="text-center">
            <button onclick="window.print()"
                    class="inline-flex items-center space-x-2 text-gray-700 hover:text-gray-900 font-semibold transition-colors duration-300 px-6 py-3 border-2 border-gray-400 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
                </svg>
                <span>Imprimir Comprobante</span>
            </button>
        </div>

        {{-- Confetti Animation --}}
        <div class="confetti-container" id="confetti"></div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@keyframes bounce-in {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-bounce-in {
    animation: bounce-in 0.8s ease-out;
}

/* Confetti animation */
.confetti-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
    z-index: 9999;
}

.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #f0f;
    animation: confetti-fall 3s linear forwards;
}

@keyframes confetti-fall {
    to {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

@media print {
    body * {
        visibility: hidden;
    }
    .max-w-4xl, .max-w-4xl * {
        visibility: visible;
    }
    .max-w-4xl {
        position: absolute;
        left: 0;
        top: 0;
    }
    button, a, .no-print {
        display: none !important;
    }
    .confetti-container {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Confetti effect
function createConfetti() {
    const container = document.getElementById('confetti');
    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff'];
    
    for (let i = 0; i < 50; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 3 + 's';
            confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
            container.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 5000);
        }, i * 30);
    }
}

// Run confetti on page load
window.addEventListener('load', () => {
    createConfetti();
    
    // Contador regresivo
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');
    
    const countdownInterval = setInterval(() => {
        countdown--;
        if (countdownElement) {
            countdownElement.textContent = countdown;
        }
        if (countdown <= 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
    
    // Redirigir automáticamente al home después de 5 segundos
    setTimeout(() => {
        window.location.href = '{{ route("votante.home") }}';
    }, 5000);
});
</script>
@endpush
@endsection