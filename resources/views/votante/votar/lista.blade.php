{{-- resources/views/votante/votar/lista.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="text-center mb-8 animate-fade-in">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-3">
                Sistema de Votación
            </h1>
            <p class="text-base sm:text-lg text-gray-600">
                {{ $eleccion->nombreEleccion }}
            </p>
            <p class="text-sm text-gray-500 mt-2">
                {{ $eleccion->descripcion }}
            </p>
        </div>

        {{-- Instructions Card --}}
        <div class="mb-8 animate-slide-down">
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 sm:p-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm sm:text-base font-medium text-blue-800">
                            Instrucciones
                        </h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Desliza para ver los candidatos. Toca para seleccionar uno por cada cargo disponible. Revisa tu selección antes de confirmar tu voto.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-2 sm:space-x-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-300"
                         :class="Object.keys(selectedCandidates).length > 0 ? 'bg-green-500 text-white' : 'bg-blue-600 text-white'">
                        <span class="text-sm sm:text-base font-semibold">1</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Selección</span>
                </div>
                <div class="h-0.5 w-12 sm:w-20 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-300"
                         :class="Object.keys(selectedCandidates).length === {{ $cargos->count() }} ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'">
                        <span class="text-sm sm:text-base font-semibold">2</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Confirmación</span>
                </div>
            </div>
        </div>

        <form id="votingForm" action="{{ route('votante.votar.emitir', $eleccion->id) }}" method="POST" x-data="votingForm()">
            @csrf

            {{-- Voting Sections by Cargo --}}
            @foreach($cargos as $cargo)
            <div class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-gray-900 text-white rounded-full font-bold text-lg sm:text-xl">
                        {{ $loop->iteration }}
                    </div>
                    <div class="ml-4 flex-1">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                            {{ $cargo->nombreCargo }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Selecciona 1 candidato
                        </p>
                    </div>
                    <div class="hidden sm:block">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300"
                              :class="selectedCandidates[{{ $cargo->id }}] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
                            <span x-show="selectedCandidates[{{ $cargo->id }}]">✓ Seleccionado</span>
                            <span x-show="!selectedCandidates[{{ $cargo->id }}]">Pendiente</span>
                        </span>
                    </div>
                </div>

                {{-- Candidates Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @forelse($candidatosPorCargo[$cargo->id] ?? [] as $candidato)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer"
                         :class="selectedCandidates[{{ $cargo->id }}] === {{ $candidato->id }} ? 'ring-4 ring-blue-500' : ''"
                         @click="selectCandidate({{ $cargo->id }}, {{ $candidato->id }})"
                         role="button"
                         tabindex="0"
                         @keydown.enter="selectCandidate({{ $cargo->id }}, {{ $candidato->id }})"
                         @keydown.space.prevent="selectCandidate({{ $cargo->id }}, {{ $candidato->id }})">
                        
                        {{-- Candidate Header --}}
                        <div class="relative">
                            @if($candidato->partido)
                            <div class="h-24 sm:h-32 flex items-center justify-center relative overflow-hidden"
                                 style="background: linear-gradient(135deg, {{ $candidato->partido->color1 }} 0%, {{ $candidato->partido->color2 ?? $candidato->partido->color1 }} 100%);">
                                <div class="text-center z-10">
                                    @if($candidato->partido->logo)
                                    <img src="{{ asset('storage/' . $candidato->partido->logo) }}" 
                                         alt="{{ $candidato->partido->nombrePartido }}"
                                         class="w-16 h-16 sm:w-20 sm:h-20 object-contain mx-auto">
                                    @endif
                                    <p class="text-white font-bold text-sm mt-2">{{ $candidato->partido->nombrePartido }}</p>
                                </div>
                                <div class="absolute inset-0 bg-black opacity-10"></div>
                            </div>
                            @else
                            <div class="h-24 sm:h-32 bg-gradient-to-r from-gray-600 to-gray-800 flex items-center justify-center">
                                <span class="text-white text-xl font-bold">INDEPENDIENTE</span>
                            </div>
                            @endif
                            
                            {{-- Selection Indicator --}}
                            <div class="absolute top-3 right-3 transition-all duration-300"
                                 x-show="selectedCandidates[{{ $cargo->id }}] === {{ $candidato->id }}"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-0"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <div class="bg-white rounded-full p-2 shadow-lg">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Candidate Info --}}
                        <div class="p-4 sm:p-6">
                            <div class="flex items-start space-x-4 mb-4">
                                @if($candidato->usuario && $candidato->usuario->perfil)
                                <img src="{{ $candidato->usuario->perfil->fotoPerfil ? asset('storage/' . $candidato->usuario->perfil->fotoPerfil) : asset('images/default-avatar.png') }}" 
                                     alt="{{ $candidato->usuario->perfil->nombres }}"
                                     class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-gray-100 shadow-md">
                                <div class="flex-1">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">
                                        {{ $candidato->usuario->perfil->nombres }} {{ $candidato->usuario->perfil->apellidoPaterno }}
                                    </h3>
                                    @if($candidato->usuario->perfil->carrera)
                                    <p class="text-xs sm:text-sm text-gray-600">
                                        {{ $candidato->usuario->perfil->carrera->nombreCarrera }}
                                    </p>
                                    @endif
                                </div>
                                @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gray-200 border-4 border-gray-100 shadow-md"></div>
                                <div class="flex-1">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">
                                        Candidato
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-600">Sin información</p>
                                </div>
                                @endif
                            </div>

                            @if($candidato->propuestas && $candidato->propuestas->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Propuestas principales:</p>
                                <ul class="space-y-1">
                                    @foreach($candidato->propuestas->take(2) as $propuesta)
                                    <li class="text-xs sm:text-sm text-gray-600 flex items-start">
                                        <span class="text-blue-500 mr-2">•</span>
                                        <span class="flex-1">{{ Str::limit($propuesta->descripcion, 60) }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- View Details Link --}}
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('votante.votar.detalle_candidato', ['eleccionId' => $eleccion->id, 'candidatoId' => $candidato->id]) }}"
                                   class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200 flex items-center"
                                   onclick="event.stopPropagation()">
                                    <span>Ver perfil completo</span>
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Select Button (Mobile) --}}
                        <div class="lg:hidden px-4 pb-4">
                            <button type="button"
                                    class="w-full py-3 rounded-lg font-semibold transition-all duration-300"
                                    :class="selectedCandidates[{{ $cargo->id }}] === {{ $candidato->id }} ? 'bg-green-500 text-white' : 'bg-blue-600 text-white hover:bg-blue-700'">
                                <span x-show="selectedCandidates[{{ $cargo->id }}] !== {{ $candidato->id }}">Seleccionar candidato</span>
                                <span x-show="selectedCandidates[{{ $cargo->id }}] === {{ $candidato->id }}">✓ Seleccionado</span>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-600 text-lg">No hay candidatos disponibles para este cargo</p>
                    </div>
                    @endforelse
                </div>

                {{-- Hidden input for this cargo --}}
                <input type="hidden" :name="'candidatos[{{ $cargo->id }}]'" x-model="selectedCandidates[{{ $cargo->id }}]">
            </div>
            @endforeach

            {{-- Confirm Button --}}
            <div class="sticky bottom-0 left-0 right-0 bg-white border-t-4 border-yellow-400 shadow-2xl p-4 sm:p-6 z-50"
                 x-show="Object.keys(selectedCandidates).length === {{ $cargos->count() }}"
                 x-transition>
                <div class="max-w-7xl mx-auto">
                    <button type="button"
                            @click="confirmVote()"
                            class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 py-4 sm:py-5 rounded-xl font-bold text-lg sm:text-xl shadow-lg hover:from-yellow-500 hover:to-yellow-600 transform hover:scale-105 transition-all duration-300 flex items-center justify-center space-x-2">
                        <span>CONFIRMAR VOTO</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <p class="text-center text-sm text-gray-600 mt-3">
                        Has seleccionado <span class="font-bold" x-text="Object.keys(selectedCandidates).length"></span> de {{ $cargos->count() }} cargos
                    </p>
                </div>
            </div>

            {{-- Incomplete selection warning --}}
            <div class="text-center py-8"
                 x-show="Object.keys(selectedCandidates).length < {{ $cargos->count() }}"
                 x-transition>
                <div class="inline-flex items-center px-6 py-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-yellow-800">
                        Debes seleccionar un candidato para cada cargo antes de continuar
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Confirmation Modal --}}
<div x-show="showConfirmModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
             @click="showConfirmModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Confirmar tu voto
                        </h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-4">
                                Por favor, revisa tu selección antes de confirmar. Una vez confirmado, no podrás cambiar tu voto.
                            </p>
                            
                            <div class="space-y-3" id="selectedCandidatesList">
                                <!-- Will be populated by Alpine.js -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button"
                        @click="submitVote()"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto">
                    Confirmar Voto
                </button>
                <button type="button"
                        @click="showConfirmModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto">
                    Revisar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function votingForm() {
    return {
        selectedCandidates: {},
        showConfirmModal: false,
        candidates: @json($candidatosPorCargo),
        cargos: @json($cargos),
        
        selectCandidate(cargoId, candidatoId) {
            this.selectedCandidates[cargoId] = candidatoId;
        },
        
        confirmVote() {
            this.showConfirmModal = true;
            this.updateConfirmationList();
        },
        
        updateConfirmationList() {
            const container = document.getElementById('selectedCandidatesList');
            container.innerHTML = '';
            
            for (const [cargoId, candidatoId] of Object.entries(this.selectedCandidates)) {
                const cargo = this.cargos.find(c => c.id == cargoId);
                const candidato = this.candidates[cargoId]?.find(c => c.id == candidatoId);
                
                if (cargo && candidato) {
                    const div = document.createElement('div');
                    div.className = 'bg-blue-50 rounded-lg p-4';
                    div.innerHTML = `
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">${cargo.nombreCargo}</p>
                        <div class="flex items-center space-x-3">
                            <img src="${candidato.usuario?.perfil?.fotoPerfil ? '/storage/' + candidato.usuario.perfil.fotoPerfil : '/images/default-avatar.png'}" 
                                 class="w-12 h-12 rounded-full object-cover border-2 border-blue-200">
                            <div>
                                <p class="font-bold text-gray-900">${candidato.usuario?.perfil?.nombres || ''} ${candidato.usuario?.perfil?.apellidoPaterno || ''}</p>
                                <p class="text-sm text-gray-600">${candidato.partido?.nombrePartido || 'Independiente'}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                }
            }
        },
        
        submitVote() {
            document.getElementById('votingForm').submit();
        }
    }
}
</script>
@endpush

@push('styles')
<style>
[x-cloak] { display: none !important; }

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-down {
    animation: slide-down 0.6s ease-out;
}
</style>
@endpush
@endsection