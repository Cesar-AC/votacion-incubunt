@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8" x-data="votingForm()">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="text-center mb-8 animate-fade-in">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-blue-900 mb-3">
                Sistema de Votación
            </h1>
            <p class="text-base sm:text-lg text-blue-700">
                {{ $eleccionActiva->titulo }}
            </p>
            <p class="text-sm text-gray-500 mt-2">
                Selecciona tus candidatos preferidos para cada cargo
            </p>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-2 sm:space-x-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-300"
                         :class="Object.keys(candidatos).length > 0 ? 'bg-green-600 text-white' : 'bg-blue-700 text-white'">
                        <span class="text-sm sm:text-base font-semibold">1</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Selección</span>
                </div>
                <div class="h-0.5 w-12 sm:w-20 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-300"
                         :class="Object.keys(candidatos).length === 6 ? 'bg-green-600 text-white' : 'bg-gray-400 text-gray-700'">
                        <span class="text-sm sm:text-base font-semibold">2</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Confirmación</span>
                </div>
            </div>
        </div>

        @include('components.error-message')

        <form id="votingForm" action="{{ route('votante.votar.emitir') }}" method="POST">
            @csrf

            {{-- Instructions --}}
            <div class="mb-8 animate-fade-in">
                <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-4 sm:p-6 shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm sm:text-base font-bold text-blue-900 mb-2">
                                Instrucciones
                            </h3>
                            <p class="mt-1 text-sm text-blue-800">
                                Selecciona un partido político (esto elegirá automáticamente a sus candidatos para Presidencia, Vicepresidencia y Coordinador). Luego, selecciona directores para cada área funcional.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Selección de Partidos --}}
            @if($partidos->isNotEmpty())
            <div class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-6 flex items-center tracking-tight">
                    <span class="bg-blue-700 text-white rounded-full p-2 mr-3 shadow-lg">
                        <i class="fas fa-users"></i>
                    </span>
                    Partidos Políticos Disponibles
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($partidos as $partido)
                        <div class="bg-white rounded-3xl shadow-lg p-6 cursor-pointer transition-all duration-500 hover:scale-105 hover:shadow-2xl relative border-4 border-blue-600"
                             :class="idPartido === {{ $partido->getKey() }} ? 'ring-4 ring-blue-600 scale-105 shadow-2xl' : ''"
                             @click="selectParty({{ $partido->getKey() }}, '{{ $partido->partido }}')"
                             data-partido-id="{{ $partido->getKey() }}"
                             data-partido-nombre="{{ $partido->partido }}">
                            <div class="text-center mb-4">
                                <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-certificate text-blue-600 text-3xl"></i>
                                </div>
                                <h3 class="font-extrabold text-xl mb-1 text-blue-600">
                                    {{ $partido->partido }}
                                </h3>
                                <p class="text-sm text-gray-600 italic">{{ $partido->descripcion ?? 'Partido político' }}</p>
                            </div>

                            @php
                                $candidatosPartido = $eleccionesService->obtenerCandidatosDePartido($partido);
                            @endphp
                            
                            @if($candidatosPartido->isNotEmpty())
                            <div class="space-y-3 mb-4">
                                @foreach($candidatosPartido as $candidato)
                                    @if($candidato->usuario && $candidato->usuario->perfil)
                                        @php
                                            $candidatoEleccion = $candidato->candidatoElecciones->first();
                                        @endphp
                                        <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                            @if($candidato->usuario->perfil?->obtenerFotoURL())
                                            <img src="{{ $candidato->usuario->perfil->obtenerFotoURL() }}" 
                                                 alt="{{ $candidato->usuario->perfil->nombre }}"
                                                 class="w-10 h-10 rounded-full object-cover border-2 border-blue-600">
                                            @else
                                            <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold text-sm">
                                                {{ substr($candidato->usuario->perfil->nombre ?? 'NC', 0, 1) }}
                                            </div>
                                            @endif
                                            <div class="flex-1">
                                                <p class="text-xs font-bold uppercase text-blue-600">
                                                    {{ $candidatoEleccion && $candidatoEleccion->cargo ? $candidatoEleccion->cargo->cargo : 'Cargo' }}
                                                </p>
                                                <p class="font-semibold text-sm text-gray-900">
                                                    {{ $candidato->usuario->perfil->nombre ?? 'Sin nombre' }} 
                                                    {{ $candidato->usuario->perfil->apellidoPaterno ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @else
                            <div class="text-center text-sm text-gray-500 py-4">
                                Este partido aún no tiene candidatos registrados
                            </div>
                            @endif
                            
                            <template x-if="idPartido === {{ $partido->getKey() }}">
                                <div class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-lg animate-bounce">
                                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </template>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Selección de Cargos por Área --}}
            @forelse($areas as $area)
            <div class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-6 flex items-center tracking-tight">
                    <span class="bg-purple-700 text-white rounded-full p-2 mr-3 shadow-lg">
                        <i class="fas fa-briefcase"></i>
                    </span>
                    {{ $area->area }}
                </h2>

                @forelse($cargoService->obtenerCargosPorArea($area) as $cargo)
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-purple-100 rounded-full p-2 mr-2">
                            <i class="fas fa-user-tie text-purple-700"></i>
                        </span>
                        {{ $cargo->cargo }}
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @forelse($candidatoService->obtenerCandidatosPorCargoEnEleccion($cargo, $eleccionActiva) as $candidato)
                        @if($candidato && $candidato->usuario && $candidato->usuario->perfil)
                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="candidatos[{{ $cargo->getKey() }}]?.id == {{ $candidato->getKey() }} ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate({{ $cargo->getKey() }}, {{ $candidato->getKey() }}, '{{ $cargo->cargo }}', '{{ $candidato->usuario->perfil->nombre }} {{ $candidato->usuario->perfil->apellidoPaterno }}')"
                             data-candidato-id="{{ $candidato->getKey() }}"
                             data-candidato-nombre="{{ $candidato->usuario->perfil->nombre }} {{ $candidato->usuario->perfil->apellidoPaterno }}"
                             data-cargo-nombre="{{ $cargo->cargo }}">
                            <div class="flex flex-col items-center text-center">
                                @if($candidato->usuario->perfil->obtenerFotoURL())
                                <img src="{{ $candidato->usuario->perfil->obtenerFotoURL() }}" 
                                     alt="{{ $candidato->usuario->perfil->nombre }}"
                                     class="w-16 h-16 rounded-full object-cover border-4 mb-3"
                                     :class="candidatos[{{ $cargo->getKey() }}]?.id == {{ $candidato->getKey() }} ? 'border-purple-600' : 'border-gray-300'">
                                @else
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl mb-3"
                                     :class="candidatos[{{ $cargo->getKey() }}]?.id == {{ $candidato->getKey() }} ? 'bg-purple-600' : 'bg-gray-400'">
                                    {{ substr($candidato->usuario->perfil?->nombre ?? 'NC', 0, 1) }}
                                </div>
                                @endif
                                
                                <h4 class="font-bold text-sm mb-1 text-gray-900">
                                    {{ $candidato->usuario->perfil?->nombre ?? 'Sin nombre' }} 
                                    {{ $candidato->usuario->perfil?->apellidoPaterno ?? '' }}
                                </h4>
                                
                                <template x-if="candidatos[{{ $cargo->getKey() }}]?.id == {{ $candidato->getKey() }}">
                                    <div class="mt-2">
                                        <svg class="w-6 h-6 text-purple-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @endif
                        @empty
                        <div class="col-span-full text-center py-8 text-gray-500">
                            No hay candidatos registrados para este cargo
                        </div>
                        @endforelse
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No hay cargos definidos para esta área</p>
                @endforelse
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">No hay áreas disponibles para votación</p>
            </div>
            @endforelse

            {{-- Submit Button --}}
            <div class="sticky bottom-0 bg-white border-t-4 border-blue-600 p-4 sm:p-6 shadow-2xl rounded-t-3xl">
                <div class="mb-4 flex items-center justify-center space-x-6 text-sm font-semibold">
                    <div :class="idPartido ? 'text-green-600' : 'text-red-600'" class="flex items-center">
                        <span class="mr-2" x-text="idPartido ? '✓' : '✗'"></span>
                        <span>Partido seleccionado</span>
                    </div>
                    <div :class="Object.keys(candidatos).length > 0 ? 'text-green-600' : 'text-red-600'" class="flex items-center">
                        <span class="mr-2" x-text="Object.keys(candidatos).length > 0 ? '✓' : '✗'"></span>
                        <span x-text="`Candidatos (${Object.keys(candidatos).length})`"></span>
                    </div>
                </div>
                
                <button type="button"
                        @click="confirmVote()"
                        :disabled="!idPartido || Object.keys(candidatos).length === 0"
                        :class="(!idPartido || Object.keys(candidatos).length === 0) ? 'opacity-50 cursor-not-allowed' : 'hover:from-blue-800 hover:to-blue-950'"
                        class="w-full bg-gradient-to-r from-blue-700 to-blue-900 text-white py-4 sm:py-5 rounded-xl font-bold text-base sm:text-lg transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <i class="fas fa-check-circle mr-2"></i>
                    Confirmar y Emitir Voto
                </button>
                
                <p class="text-xs text-gray-500 mt-3 text-center">
                    Ambos campos son obligatorios: Selecciona un partido y al menos un candidato
                </p>
            </div>
            
            <!-- Hidden inputs for form submission -->
            <input type="hidden" name="idPartido" :value="idPartido">
            <template x-for="(candidatoInfo, cargoId) in candidatos">
                <input type="hidden" name="candidatos[]" :value="candidatoInfo.id">
            </template>
        </form>

        {{-- Confirmation Modal --}}
        <div x-show="showConfirmModal" 
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             @click.self="showConfirmModal = false">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-gradient-to-r from-blue-700 to-blue-900 text-white p-6 rounded-t-3xl">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        Confirmar tu Voto
                    </h2>
                </div>
                
                <div class="p-6">
                    <p class="text-gray-700 mb-6">
                        Por favor revisa tu selección antes de confirmar. Una vez emitido, <strong>no podrás cambiar tu voto</strong>.
                    </p>
                    
                    <div class="space-y-4 mb-6">
                        {{-- Partido seleccionado --}}
                        <template x-if="partidoInfo">
                            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded-lg">
                                <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                                    <i class="fas fa-users mr-2"></i>
                                    Partido Seleccionado:
                                </h3>
                                <p class="text-blue-700 font-semibold text-lg ml-6" x-text="partidoInfo.nombre"></p>
                            </div>
                        </template>
                        
                        {{-- Candidatos seleccionados --}}
                        <template x-if="Object.keys(candidatos).length > 0">
                            <div class="bg-purple-50 border-l-4 border-purple-600 p-4 rounded-lg">
                                <h3 class="font-bold text-purple-900 mb-3 flex items-center">
                                    <i class="fas fa-user-check mr-2"></i>
                                    Candidatos Seleccionados:
                                </h3>
                                <template x-for="(candidatoInfo, cargoId) in candidatos">
                                    <div class="bg-white p-3 mb-2 rounded-lg shadow-sm">
                                        <p class="font-bold text-purple-700 text-sm mb-1" x-text="candidatoInfo.cargo"></p>
                                        <p class="text-gray-800 font-semibold" x-text="candidatoInfo.nombre"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2 flex-shrink-0 mt-0.5"></i>
                            <p class="text-sm text-yellow-800">
                                <strong>Importante:</strong> Tu voto es secreto y no podrá ser modificado después de confirmar.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button"
                                @click="showConfirmModal = false"
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 rounded-xl font-bold transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="button"
                                @click="submitVote()"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold transition-colors duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Emitir Voto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success Modal --}}
        <div x-show="showSuccessModal" 
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full text-center p-8 animate-bounce">
                <div class="mb-6">
                    <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-5xl"></i>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-green-700 mb-3">
                    ¡Voto Registrado Exitosamente!
                </h2>
                <p class="text-gray-700 mb-2">
                    Tu voto ha sido emitido correctamente.
                </p>
                <p class="text-sm text-gray-600">
                    Serás redirigido a la confirmación en unos momentos...
                </p>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-center space-x-1">
                        <span class="text-gray-600 text-sm">Redirigiendo en</span>
                        <span class="font-bold text-green-600">3</span>
                        <span class="text-gray-600 text-sm">segundos...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}
</style>
@endpush

@push('scripts')
<script>
function votingForm() {
    return {
        idPartido: null,
        partidoInfo: null,
        candidatos: {},
        showConfirmModal: false,
        showSuccessModal: false,
        
        selectParty(partidoId, partidoNombre) {
            console.log('Partido seleccionado:', partidoId, partidoNombre);
            this.idPartido = partidoId;
            this.partidoInfo = {
                id: partidoId,
                nombre: partidoNombre
            };
        },
        
        selectCandidate(cargoId, candidatoId, cargoNombre, candidatoNombre) {
            console.log('Candidato seleccionado:', cargoId, candidatoId, cargoNombre, candidatoNombre);
            this.candidatos[cargoId] = {
                id: candidatoId,
                nombre: candidatoNombre,
                cargo: cargoNombre
            };
        },
        
        confirmVote() {
            console.log('Confirmando voto...');
            console.log('Partido:', this.idPartido);
            console.log('Candidatos:', this.candidatos);
            
            if (!this.idPartido) {
                alert('❌ DEBE seleccionar un PARTIDO POLÍTICO');
                return;
            }
            
            const candidatosCount = Object.keys(this.candidatos).length;
            if (candidatosCount === 0) {
                alert('❌ DEBE seleccionar al menos UN CANDIDATO');
                return;
            }
            
            this.showConfirmModal = true;
        },
        
        submitVote() {
            console.log('Enviando voto...');
            this.showSuccessModal = true;
            this.showConfirmModal = false;
            
            setTimeout(() => {
                console.log('Enviando formulario...');
                document.getElementById('votingForm').submit();
            }, 1000);
        }
    }
}
</script>
@endpush

@push('styles')
<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}
</style>
@endpush
@endsection