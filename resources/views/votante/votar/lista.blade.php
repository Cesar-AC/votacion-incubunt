{{-- resources/views/votante/votar/lista.blade.php --}}
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
                Elecciones INCUBUNT 2026
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
                         :class="Object.keys(selectedCandidates).length > 0 ? 'bg-green-600 text-white' : 'bg-blue-700 text-white'">
                        <span class="text-sm sm:text-base font-semibold">1</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Selección</span>
                </div>
                <div class="h-0.5 w-12 sm:w-20 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-300"
                         :class="Object.keys(selectedCandidates).length === votosRequeridos ? 'bg-green-600 text-white' : 'bg-gray-400 text-gray-700'">
                        <span class="text-sm sm:text-base font-semibold">2</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Confirmación</span>
                </div>
            </div>
        </div>

        <form id="votingForm" action="#" method="POST" data-eleccion-id="{{ $eleccion->idElecciones }}">
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

            {{-- Selección de Partidos Reales --}}
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
                         :class="selectedParty === {{ $partido->idPartido }} ? 'ring-4 ring-blue-600 scale-105 shadow-2xl' : ''"
                         @click="selectParty({{ $partido->idPartido }})">
                        
                        <div class="text-center mb-4">
                            <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-certificate text-blue-600 text-3xl"></i>
                            </div>
                            <h3 class="font-extrabold text-xl mb-1 text-blue-600">
                                {{ $partido->partido }}
                            </h3>
                            <p class="text-sm text-gray-600 italic">{{ $partido->descripcion ?? 'Partido político' }}</p>
                        </div>

                        <div class="space-y-3 mb-4">
                            @foreach($partido->candidatos->take(3) as $candidato)
                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3"
                                 data-candidato-id="{{ $candidato->idCandidato }}"
                                 data-candidato-nombre="{{ $candidato->usuario->perfil->nombre ?? 'Sin nombre' }}"
                                 data-candidato-apellido="{{ $candidato->usuario->perfil->apellidoPaterno ?? '' }}"
                                 data-cargo-nombre="{{ $candidato->cargo->cargo ?? 'Cargo' }}"
                                 data-partido-id="{{ $partido->idPartido }}"
                                 data-partido-nombre="{{ $partido->partido }}">
                                @if($candidato->usuario && $candidato->usuario->perfil && $candidato->usuario->perfil->fotoPerfil)
                                <img src="{{ asset('storage/' . $candidato->usuario->perfil->fotoPerfil) }}" 
                                     alt="{{ $candidato->usuario->perfil->nombre }}"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-blue-600">
                                @else
                                <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold text-sm">
                                    {{ substr($candidato->usuario->perfil->nombre ?? 'NC', 0, 1) }}
                                </div>
                                @endif
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-blue-600">{{ $candidato->cargo->cargo ?? 'Cargo' }}</p>
                                    <p class="font-semibold text-sm text-gray-900">
                                        {{ $candidato->usuario->perfil->nombre ?? 'Sin nombre' }} 
                                        {{ $candidato->usuario->perfil->apellidoPaterno ?? '' }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <template x-if="selectedParty === {{ $partido->idPartido }}">
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

                @forelse($area->cargos as $cargo)
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-purple-100 rounded-full p-2 mr-2">
                            <i class="fas fa-user-tie text-purple-700"></i>
                        </span>
                        {{ $cargo->nombreCargo ?? $cargo->cargo }}
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @forelse($cargo->candidatos as $candidato)
                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="selectedCandidates[{{ $cargo->idCargo }}] === {{ $candidato->idCandidato }} ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate({{ $cargo->idCargo }}, {{ $candidato->idCandidato }})"
                             data-candidato-id="{{ $candidato->idCandidato }}"
                             data-candidato-nombre="{{ $candidato->usuario->perfil->nombre ?? 'Sin nombre' }}"
                             data-candidato-apellido="{{ $candidato->usuario->perfil->apellidoPaterno ?? '' }}"
                             data-cargo-nombre="{{ $cargo->cargo ?? 'Cargo' }}"
                             data-cargo-id="{{ $cargo->idCargo }}"
                             data-partido-id="{{ $candidato->idPartido ?? '' }}"
                             data-partido-nombre="{{ $candidato->partido->partido ?? 'Independiente' }}">
                            <div class="flex items-center space-x-3">
                                @if($candidato->usuario && $candidato->usuario->perfil && $candidato->usuario->perfil->fotoPerfil)
                                <img src="{{ asset('storage/' . $candidato->usuario->perfil->fotoPerfil) }}" 
                                     alt="{{ $candidato->usuario->perfil->nombre }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-300">
                                @else
                                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold">
                                    {{ substr($candidato->usuario->perfil->nombre ?? 'NC', 0, 1) }}
                                </div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">
                                        {{ $candidato->usuario->perfil->nombre ?? 'Sin nombre' }} 
                                        {{ $candidato->usuario->perfil->apellidoPaterno ?? '' }}
                                    </p>
                                    @if($candidato->partido)
                                    <p class="text-sm text-gray-600">{{ $candidato->partido->partido }}</p>
                                    @else
                                    <p class="text-sm text-gray-600">Independiente</p>
                                    @endif
                                </div>
                                <template x-if="selectedCandidates[{{ $cargo->idCargo }}] === {{ $candidato->idCandidato }}">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-4 text-gray-500">
                            <p>No hay candidatos disponibles para este cargo</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No hay cargos disponibles en esta área</p>
                </div>
                @endforelse
            </div>
            @empty
            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-6 mb-8">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 text-xl"></i>
                    <div>
                        <h3 class="font-bold text-yellow-800 mb-2">No hay candidatos disponibles</h3>
                        <p class="text-yellow-700">Por el momento no hay candidatos registrados para esta elección. Los datos se mostrarán cuando estén disponibles.</p>
                    </div>
                </div>
            </div>
            @endforelse

            {{-- Confirmation Button --}}
            <div class="sticky bottom-0 bg-white border-t-4 border-blue-600 p-6 shadow-2xl rounded-t-3xl">
                <div class="max-w-4xl mx-auto">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-center sm:text-left">
                            <p class="text-sm text-gray-600">Votos seleccionados</p>
                            <p class="text-2xl font-bold text-blue-900">
                                <span x-text="getVotosActuales()"></span> / <span x-text="votosRequeridos"></span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1" x-show="selectedParty !== null">
                                <i class="fas fa-info-circle"></i> 
                                Partido: 1 voto + <span x-text="getVotosDirectores()"></span> directores
                            </p>
                        </div>
                        <button type="button"
                                @click="confirmVote()"
                                :disabled="getVotosActuales() !== votosRequeridos"
                                :class="getVotosActuales() === votosRequeridos ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="w-full sm:w-auto px-8 py-4 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl text-lg">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirmar y Votar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Hidden Inputs --}}
            <template x-for="(candidatoId, cargoId) in selectedCandidates" :key="cargoId">
                <input type="hidden" :name="'candidatos[' + cargoId + ']'" :value="candidatoId">
            </template>
        </form>

        {{-- Confirmation Modal --}}
        <div x-show="showConfirmModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             @click.self="showConfirmModal = false"
             style="display: none;">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 @click.stop>
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
                    
                    <div id="selectedCandidatesList" class="space-y-3 mb-6">
                        {{-- Populated by JavaScript --}}
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
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
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             style="display: none;">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full text-center p-8"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100">
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

@push('scripts')
<script>
// Verificar que Alpine.js está cargado
document.addEventListener('alpine:init', () => {
    console.log('✅ Alpine.js inicializado correctamente');
});

function votingForm() {
    return {
        selectedCandidates: {},
        selectedParty: null,
        showConfirmModal: false,
        showSuccessModal: false,
        votosRequeridos: {{ $votosRequeridos ?? 0 }},
        partidosHabilitados: {{ $partidosHabilitados ?? 0 }},
        
        init() {
            console.log('✅ Componente votingForm inicializado');
            console.log('Votos requeridos:', this.votosRequeridos);
            console.log('Partidos habilitados:', this.partidosHabilitados);
            
            // Forzar que Alpine reconozca los cambios de estado
            this.$watch('showConfirmModal', value => {
                console.log('📋 Modal de confirmación cambió a:', value);
            });
            
            this.$watch('showSuccessModal', value => {
                console.log('✅ Modal de éxito cambió a:', value);
            });
        },
        
        selectParty(partidoId) {
            this.selectedParty = partidoId;
            
            // Buscar los candidatos del partido en el DOM y seleccionarlos automáticamente
            const partidoElements = document.querySelectorAll(`[data-partido-id="${partidoId}"]`);
            
            partidoElements.forEach(element => {
                const candidatoId = parseInt(element.getAttribute('data-candidato-id'));
                const cargoNombre = element.getAttribute('data-cargo-nombre');
                
                // Solo auto-seleccionar para cargos de partido (Presidencia, Vicepresidencia, Coordinador)
                if (cargoNombre === 'Presidencia' || cargoNombre === 'Vicepresidencia' || cargoNombre === 'Coordinador') {
                    // Determinar el cargo ID basado en el nombre
                    let cargoId;
                    if (cargoNombre === 'Presidencia') cargoId = 1;
                    else if (cargoNombre === 'Vicepresidencia') cargoId = 2;
                    else if (cargoNombre === 'Coordinador') cargoId = 3;
                    
                    this.selectedCandidates[cargoId] = candidatoId;
                }
            });
            
            console.log('Partido seleccionado:', partidoId);
            console.log('Candidatos actualizados:', this.selectedCandidates);
        },
        
        selectCandidate(cargoId, candidatoId) {
            this.selectedCandidates[cargoId] = candidatoId;
            console.log(`Candidato ${candidatoId} seleccionado para cargo ${cargoId}`);
        },
        
        getVotosActuales() {
            let votos = 0;
            
            // Si hay un partido seleccionado, cuenta como 1 voto
            if (this.selectedParty !== null) {
                votos += 1;
            }
            
            // Contar votos individuales (excluyendo cargos de partido: 1, 2, 3)
            const cargosPartido = [1, 2, 3]; // Presidencia, Vicepresidencia, Coordinador
            for (const cargoId in this.selectedCandidates) {
                if (!cargosPartido.includes(parseInt(cargoId))) {
                    votos += 1;
                }
            }
            
            return votos;
        },
        
        getVotosDirectores() {
            let directores = 0;
            
            // Contar votos individuales (excluyendo cargos de partido: 1, 2, 3)
            const cargosPartido = [1, 2, 3];
            for (const cargoId in this.selectedCandidates) {
                if (!cargosPartido.includes(parseInt(cargoId))) {
                    directores += 1;
                }
            }
            
            return directores;
        },
        
        confirmVote() {
            console.log('🔵 confirmVote() llamado');
            
            // Contar votos: partido cuenta como 1 voto, resto son individuales
            let votosActuales = 0;
            
            // Si hay un partido seleccionado, cuenta como 1 voto
            if (this.selectedParty !== null) {
                votosActuales += 1;
            }
            
            // Contar votos individuales (excluyendo cargos de partido: 1, 2, 3)
            const cargosPartido = [1, 2, 3]; // Presidencia, Vicepresidencia, Coordinador
            for (const cargoId in this.selectedCandidates) {
                if (!cargosPartido.includes(parseInt(cargoId))) {
                    votosActuales += 1;
                }
            }
            
            console.log('Partido seleccionado:', this.selectedParty);
            console.log('Votos actuales:', votosActuales);
            console.log('Votos requeridos:', this.votosRequeridos);
            console.log('Candidatos seleccionados:', this.selectedCandidates);
            
            if (votosActuales === 0) {
                alert('Por favor selecciona al menos un candidato o partido.');
                return;
            }
            
            if (this.votosRequeridos > 0 && votosActuales < this.votosRequeridos) {
                const mensaje = this.selectedParty 
                    ? `Debes seleccionar ${this.votosRequeridos} votos. Has seleccionado: 1 partido + ${votosActuales - 1} directores = ${votosActuales} votos.`
                    : `Debes seleccionar ${this.votosRequeridos} candidato(s). Has seleccionado ${votosActuales}.`;
                alert(mensaje);
                return;
            }
            
            console.log('✅ Validaciones pasadas, mostrando modal...');
            
            // Actualizar la lista de candidatos antes de mostrar el modal
            this.updateConfirmationList();
            
            // Mostrar el modal usando Alpine.js
            this.showConfirmModal = true;
            
            console.log('showConfirmModal =', this.showConfirmModal);
        },
        
        updateConfirmationList() {
            const container = document.getElementById('selectedCandidatesList');
            if (!container) {
                console.error('❌ No se encontró el contenedor selectedCandidatesList');
                return;
            }
            
            container.innerHTML = '';
            
            console.log('📋 Actualizando lista de confirmación...');
            
            // Obtener información de candidatos desde el DOM
            for (const [cargoId, candidatoId] of Object.entries(this.selectedCandidates)) {
                // Buscar el elemento del candidato seleccionado en el DOM para obtener datos reales
                const candidatoElement = document.querySelector(`[data-candidato-id="${candidatoId}"]`);
                
                if (candidatoElement) {
                    const nombre = candidatoElement.getAttribute('data-candidato-nombre');
                    const apellido = candidatoElement.getAttribute('data-candidato-apellido') || '';
                    const cargo = candidatoElement.getAttribute('data-cargo-nombre');
                    const partido = candidatoElement.getAttribute('data-partido-nombre') || 'Independiente';
                    
                    console.log(`- ${cargo}: ${nombre} ${apellido} (${partido})`);
                    
                    const div = document.createElement('div');
                    div.className = 'bg-blue-50 rounded-lg p-4 border-2 border-blue-200';
                    div.innerHTML = `
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">${cargo}</p>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold">
                                ${nombre.charAt(0)}${apellido.charAt(0) || ''}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">${nombre} ${apellido}</p>
                                <p class="text-sm text-gray-600">${partido}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                } else {
                    console.warn(`⚠️ No se encontró elemento para candidato ${candidatoId}`);
                }
            }
        },
        
        submitVote() {
            console.log('🚀 Iniciando envío de voto...');
            
            // Construir el objeto de votos con información de candidatos
            const votos = {};
            
            for (const [cargoId, candidatoId] of Object.entries(this.selectedCandidates)) {
                // Buscar el elemento del candidato en el DOM para obtener datos reales
                const candidatoElement = document.querySelector(`[data-candidato-id="${candidatoId}"]`);
                
                if (candidatoElement) {
                    const nombre = candidatoElement.getAttribute('data-candidato-nombre');
                    const apellido = candidatoElement.getAttribute('data-candidato-apellido') || '';
                    const cargo = candidatoElement.getAttribute('data-cargo-nombre');
                    const partidoId = candidatoElement.getAttribute('data-partido-id');
                    const partidoNombre = candidatoElement.getAttribute('data-partido-nombre') || 'Independiente';
                    
                    votos[cargoId] = {
                        candidatoId: parseInt(candidatoId),
                        cargoId: parseInt(cargoId),
                        nombre: nombre,
                        apellido: apellido,
                        cargo: cargo,
                        partidoId: partidoId ? parseInt(partidoId) : null,
                        partidoNombre: partidoNombre
                    };
                    
                    console.log(`Cargo ${cargoId}: ${nombre} ${apellido} (${cargo}) - Partido: ${partidoNombre}`);
                }
            }
            
            console.log('Votos a registrar:', votos);
            console.log('Partido seleccionado:', this.selectedParty);
            
            // Enviar al servidor
            const form = document.getElementById('votingForm');
            const eleccionId = form.getAttribute('data-eleccion-id');
            
            const datosVoto = {
                candidatos: votos,
                partidoSeleccionado: this.selectedParty,
                _token: document.querySelector('input[name="_token"]').value
            };
            
            console.log('Datos enviados al servidor:', datosVoto);
            
            fetch(`/votante/votar/${eleccionId}/emitir`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(datosVoto)
            })
            .then(response => {
                console.log('Respuesta del servidor - Estado:', response.status);
                if (response.status === 200 || response.status === 201) {
                    return response.json();
                } else {
                    return response.json().then(err => {
                        console.error('Error en respuesta:', err);
                        throw new Error(err.message || 'Error al registrar el voto');
                    });
                }
            })
            .then(data => {
                console.log('✅ Respuesta exitosa:', data);
                this.showSuccessModal = true;
                this.showConfirmModal = false;
                
                // Redirigir después de 3 segundos
                setTimeout(() => {
                    window.location.href = `/votante/votar/${eleccionId}/exito`;
                }, 3000);
            })
            .catch(error => {
                console.error('❌ Error en la petición:', error);
                alert('Error al registrar el voto: ' + error.message);
                this.showSuccessModal = false;
                this.showConfirmModal = true;
            });
        }
    }
}
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* Ocultar elementos con x-cloak hasta que Alpine.js esté listo */
[x-cloak] { 
    display: none !important; 
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

/* Animación para el modal de éxito */
@keyframes bounce-in {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

/* Asegurar que los modales estén visibles cuando Alpine.js los active */
div[x-show] {
    transition: opacity 0.3s ease;
}
</style>
@endpush
@endsection