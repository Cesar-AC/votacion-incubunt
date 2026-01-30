{{-- resources/views/votante/votar/lista.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
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

        <form id="votingForm" action="#" method="POST" x-data="votingForm()">
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
                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
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
                             @click="selectCandidate({{ $cargo->idCargo }}, {{ $candidato->idCandidato }})">
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
                                <span x-text="Object.keys(selectedCandidates).length"></span> / <span x-text="votosRequeridos"></span>
                            </p>
                        </div>
                        <button type="button"
                                @click="confirmVote()"
                                :disabled="Object.keys(selectedCandidates).length !== votosRequeridos"
                                :class="Object.keys(selectedCandidates).length === votosRequeridos ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="w-full sm:w-auto px-8 py-4 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl text-lg">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirmar y Votar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Hidden Inputs --}}
            <template x-for="(candidatoId, cargoId) in selectedCandidates">
                <input type="hidden" :name="'candidatos[' + cargoId + ']'" :value="candidatoId">
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

@push('scripts')
<script>
function votingForm() {
    return {
        selectedCandidates: {},
        selectedParty: null,
        showConfirmModal: false,
        showSuccessModal: false,
        votosRequeridos: {{ $votosRequeridos ?? 0 }},
        partidosHabilitados: {{ $partidosHabilitados ?? 0 }},
        
        // Datos estáticos de candidatos
        candidatesData: {
            1: { cargo: 'Presidencia', nombre: 'Carlos Mendez', partido: 'Sinergia' },
            2: { cargo: 'Vicepresidencia', nombre: 'Ana Torres', partido: 'Sinergia' },
            3: { cargo: 'Coordinador', nombre: 'Luis Puma', partido: 'Sinergia' },
            11: { cargo: 'Presidencia', nombre: 'Juan Verde', partido: 'Progreso' },
            12: { cargo: 'Vicepresidencia', nombre: 'Maria Flores', partido: 'Progreso' },
            13: { cargo: 'Coordinador', nombre: 'Pedro Silva', partido: 'Progreso' },
            21: { cargo: 'Presidencia', nombre: 'Sofia Ramos', partido: 'Unidad' },
            22: { cargo: 'Vicepresidencia', nombre: 'Diego Vargas', partido: 'Unidad' },
            23: { cargo: 'Coordinador', nombre: 'Laura Castro', partido: 'Unidad' },
            31: { cargo: 'Director de Marketing', nombre: 'Roberto Marketing', partido: 'Independiente' },
            32: { cargo: 'Director de Marketing', nombre: 'Lucia Brand', partido: 'Independiente' },
            41: { cargo: 'Director de Finanzas', nombre: 'Carmen Finanzas', partido: 'Independiente' },
            42: { cargo: 'Director de Finanzas', nombre: 'Jorge Contador', partido: 'Independiente' },
            51: { cargo: 'Director de RRHH', nombre: 'Patricia RRHH', partido: 'Independiente' },
            52: { cargo: 'Director de RRHH', nombre: 'Miguel Talento', partido: 'Independiente' },
        },
        
        selectParty(partidoId, presidenteId, vicepresidenteId, coordinadorId) {
            this.selectedParty = partidoId;
            
            // Cargar automáticamente los candidatos del partido
            this.selectedCandidates[1] = presidenteId;
            this.selectedCandidates[2] = vicepresidenteId;
            this.selectedCandidates[3] = coordinadorId;
        },
        
        selectCandidate(cargoId, candidatoId) {
            this.selectedCandidates[cargoId] = candidatoId;
        },
        
        confirmVote() {
            // Validar que tenga los votos requeridos
            const votosActuales = Object.keys(this.selectedCandidates).length;
            
            if (votosActuales === 0) {
                alert('Por favor selecciona al menos un candidato.');
                return;
            }
            
            if (this.votosRequeridos > 0 && votosActuales < this.votosRequeridos) {
                alert(`Debes seleccionar ${this.votosRequeridos} candidato(s). Has seleccionado ${votosActuales}.`);
                return;
            }
            
            this.showConfirmModal = true;
            this.updateConfirmationList();
        },
        
        updateConfirmationList() {
            const container = document.getElementById('selectedCandidatesList');
            container.innerHTML = '';
            
            for (const [cargoId, candidatoId] of Object.entries(this.selectedCandidates)) {
                const candidato = this.candidatesData[candidatoId];
                
                if (candidato) {
                    const div = document.createElement('div');
                    div.className = 'bg-blue-50 rounded-lg p-4 border-2 border-blue-200';
                    div.innerHTML = `
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">${candidato.cargo}</p>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold">
                                ${candidato.nombre.split(' ').map(n => n[0]).join('')}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">${candidato.nombre}</p>
                                <p class="text-sm text-gray-600">${candidato.partido}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                }
            }
        },
        
        handleSubmit() {
            // Construir el objeto de candidatos
            const candidatos = {};
            for (const [cargoId, candidatoId] of Object.entries(this.selectedCandidates)) {
                candidatos[cargoId] = candidatoId;
            }
            
            // Validar que haya candidatos seleccionados
            if (Object.keys(candidatos).length === 0) {
                alert('Por favor selecciona al menos un candidato.');
                return;
            }
            
            // Crear un formulario temporal para enviar los datos
            const form = document.getElementById('votingForm');
            const formData = new FormData(form);
            
            // Agregar los candidatos seleccionados
            for (const [cargoId, candidatoId] of Object.entries(candidatos)) {
                formData.append('candidatos[' + cargoId + ']', candidatoId);
            }
            
            // Mostrar modal de éxito
            this.showSuccessModal = true;
            this.showConfirmModal = false;
            
            // Enviar el formulario después de 1 segundo
            setTimeout(() => {
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok || response.status === 302 || response.status === 200) {
                        // Esperar 2 segundos más y luego redirigir a la página de éxito
                        setTimeout(() => {
                            // Obtener el ID de la elección desde el atributo data del formulario
                            const eleccionId = form.getAttribute('data-eleccion-id');
                            window.location.href = `/votante/votar/${eleccionId}/exito`;
                        }, 2000);
                    } else {
                        throw new Error('Error en la respuesta del servidor');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al registrar el voto. Por favor intenta de nuevo.');
                    this.showSuccessModal = false;
                    this.showConfirmModal = true;
                });
            }, 1000);
        }
    }
}
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
[x-cloak] { display: none !important; }

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