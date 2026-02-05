@extends('layouts.admin')

@section('content')
{{-- Preparación de datos para Alpine --}}
@php
    $partidosData = $partidos->map(function($pe) {
        $p = $pe->partido;
        return [
            'id' => $p->getKey(),
            'nombre' => $p->partido,
            'descripcion' => $p->descripcion ?? 'Sin descripción disponible.',
            'logo_text' => strtoupper(substr($p->partido, 0, 2)),
            
            // Cargamos propuestas del partido
            'propuestas' => $p->propuestas->map(fn($pr) => [
                'titulo' => $pr->propuesta,
                'descripcion' => $pr->descripcion
            ])->values(),
            
            // Cargamos candidatos
            'candidatos' => $p->candidatos->take(10)->map(fn($c) => [
                'id' => $c->getKey(),
                'nombre' => trim(($c->usuario->perfil->nombre ?? '') . ' ' . ($c->usuario->perfil->apellidoPaterno ?? '')),
                'cargo' => $c->cargo->cargo ?? 'Miembro',
                'foto' => $c->usuario->perfil->fotoPerfil ?? null,
                'initials' => strtoupper(substr($c->usuario->perfil->nombre ?? '?', 0, 1))
            ])->values()
        ];
    })->values();
@endphp

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8" 
     x-data="{ 
        partidos: {{ Js::from($partidosData) }},
        selectedParty: null,
        modalOpen: false,
        currentParty: null,

        openModal(partyId) {
            this.currentParty = this.partidos.find(p => p.id === partyId);
            this.modalOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.modalOpen = false;
            setTimeout(() => { this.currentParty = null; }, 300); // Wait for transition
            document.body.style.overflow = '';
        },

        selectFromModal() {
            if (this.currentParty) {
                this.selectedParty = this.currentParty.id;
                this.closeModal();
                
                // Scroll suave hacia la sección de confirmación
                this.$nextTick(() => {
                    const confirmSection = document.getElementById('confirmation-section');
                    if(confirmSection) {
                        confirmSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        }
     }">
    
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Elige tu partido para la presidencia</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Conoce las propuestas y candidatos antes de decidir. Selecciona un partido para confirmar tu voto.</p>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('votante.votar.emitir', ['eleccionId' => $eleccionActiva->getKey()]) }}" method="POST" id="voteForm">
            @csrf
            <input type="hidden" name="partido_id" x-model="selectedParty">

            {{-- Grid de Partidos --}}
            @if($partidos->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    <template x-for="partido in partidos" :key="partido.id">
                        <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-100 cursor-pointer flex flex-col h-full"
                             @click="openModal(partido.id)">
                            
                            {{-- Indicador de selección en la tarjeta (visual feedback) --}}
                            <div class="absolute top-0 right-0 p-0 z-20" x-show="selectedParty === partido.id">
                                <div class="bg-indigo-600 text-white rounded-bl-2xl px-3 py-1 font-bold text-xs shadow-md">
                                    SELECCIONADO
                                </div>
                            </div>

                             {{-- Borde activo --}}
                            <div class="absolute inset-0 border-2 rounded-2xl transition-colors duration-200 pointer-events-none z-10"
                                 :class="selectedParty === partido.id ? 'border-indigo-600' : 'border-transparent group-hover:border-indigo-100'">
                            </div>

                            {{-- Header Gradiente --}}
                            <div class="h-3 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600 group-hover:h-4 transition-all duration-300"></div>

                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl shadow-inner">
                                        <span x-text="partido.logo_text"></span>
                                    </div>
                                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">Ver detalles</span>
                                </div>

                                <h3 class="text-2xl font-bold text-gray-900 mb-3" x-text="partido.nombre"></h3>
                                <p class="text-gray-500 text-sm leading-relaxed mb-6 line-clamp-3" x-text="partido.descripcion"></p>
                                
                                {{-- Preview de Candidatos --}}
                                <div class="mt-auto border-t border-gray-50 pt-4">
                                    <p class="text-xs text-gray-400 font-medium mb-3 uppercase tracking-wider">Candidatos principales</p>
                                    <div class="flex -space-x-2 overflow-hidden py-1">
                                        <template x-for="(candidato, idx) in partido.candidatos.slice(0, 5)" :key="candidato.id">
                                            <div class="relative inline-block h-8 w-8 rounded-full ring-2 ring-white bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 overflow-hidden" 
                                                 :title="candidato.nombre">
                                                <template x-if="candidato.foto">
                                                    <img :src="candidato.foto" class="h-full w-full object-cover">
                                                </template>
                                                <template x-if="!candidato.foto">
                                                    <span x-text="candidato.initials"></span>
                                                </template>
                                            </div>
                                        </template>
                                        <div x-show="partido.candidatos.length > 5" class="relative inline-block h-8 w-8 rounded-full ring-2 ring-white bg-gray-50 flex items-center justify-center text-xs font-medium text-gray-500">
                                            +<span x-text="partido.candidatos.length - 5"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @else
                <div class="text-center py-16 px-4 bg-white rounded-3xl shadow-sm border border-gray-100 max-w-2xl mx-auto">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No hay partidos registrados</h3>
                    <p class="text-gray-500">En este momento no hay partidos políticos disponibles para esta elección.</p>
                </div>
            @endif

            {{-- Sección de Confirmación (ESTÁTICA AL FINAL) --}}
            <div id="confirmation-section" 
                 x-show="selectedParty !== null"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="bg-white rounded-2xl shadow-lg border border-indigo-100 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6"
                 style="display: none;">
                
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Has seleccionado votar por:</p>
                        <h3 class="text-2xl font-bold text-gray-900" x-text="partidos.find(p => p.id === selectedParty)?.nombre"></h3>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <button type="button" @click="selectedParty = null" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cambiar elección
                    </button>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span>Confirmar Voto</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- MODAL DE DETALLE DE PARTIDO --}}
    <div x-show="modalOpen" class="relative z-[60]" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        
        {{-- Backdrop --}}
        <div x-show="modalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" 
             @click="closeModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- Modal Panel --}}
                <div x-show="modalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-gray-100"
                     @click.stop>
                    
                    <template x-if="currentParty">
                        <div>
                            {{-- Modal Header --}}
                            <div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 px-6 py-8 sm:py-10 text-white overflow-hidden">
                                {{-- Decorative shapes --}}
                                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5"></div>
                                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-40 h-40 rounded-full bg-white opacity-5"></div>
                                
                                <button type="button" @click="closeModal()" class="absolute top-4 right-4 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition-colors">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="relative flex flex-col items-center text-center">
                                    <div class="h-20 w-20 rounded-2xl bg-white text-indigo-600 flex items-center justify-center text-3xl font-bold shadow-lg mb-4">
                                        <span x-text="currentParty.logo_text"></span>
                                    </div>
                                    <h3 class="text-3xl font-bold tracking-tight" x-text="currentParty.nombre"></h3>
                                    <p class="mt-2 text-indigo-100 max-w-lg" x-text="currentParty.descripcion"></p>
                                </div>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-6 py-6 sm:px-10 max-h-[60vh] overflow-y-auto">
                                
                                {{-- Propuestas --}}
                                <div class="mb-10">
                                    <h4 class="flex items-center text-lg font-bold text-gray-900 mb-4">
                                        <span class="w-1 h-6 bg-indigo-500 rounded-full mr-3"></span>
                                        Propuestas Principales
                                    </h4>
                                    
                                    <template x-if="currentParty.propuestas.length > 0">
                                        <ul class="space-y-3">
                                            <template x-for="propuesta in currentParty.propuestas" :key="propuesta.titulo">
                                                <li class="bg-gray-50 rounded-xl p-4 flex gap-3 items-start">
                                                    <div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-gray-900 text-sm" x-text="propuesta.titulo"></p>
                                                        <p class="text-gray-600 text-sm mt-1" x-text="propuesta.descripcion"></p>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>
                                    <template x-if="currentParty.propuestas.length === 0">
                                        <p class="text-gray-400 italic text-sm text-center py-4 bg-gray-50 rounded-xl">No hay propuestas registradas.</p>
                                    </template>
                                </div>

                                {{-- Candidatos --}}
                                <div>
                                    <h4 class="flex items-center text-lg font-bold text-gray-900 mb-4">
                                        <span class="w-1 h-6 bg-purple-500 rounded-full mr-3"></span>
                                        Equipo de Candidatos
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <template x-for="candidato in currentParty.candidatos" :key="candidato.id">
                                            <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-200 overflow-hidden relative">
                                                    <template x-if="candidato.foto">
                                                        <img :src="candidato.foto" class="h-full w-full object-cover">
                                                    </template>
                                                    <template x-if="!candidato.foto">
                                                        <div class="h-full w-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-500 font-bold text-xs">
                                                            <span x-text="candidato.initials"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <p class="font-semibold text-gray-900 text-sm truncate" x-text="candidato.nombre"></p>
                                                    <p class="text-xs text-gray-500 uppercase tracking-wide truncate" x-text="candidato.cargo"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <template x-if="currentParty.candidatos.length === 0">
                                        <p class="text-gray-400 italic text-sm text-center py-4 bg-gray-50 rounded-xl">No hay candidatos visibles.</p>
                                    </template>
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:px-10 border-t border-gray-100">
                                <button type="button" 
                                        @click="selectFromModal()" 
                                        class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 sm:ml-3 sm:w-auto transition-transform active:scale-95">
                                    Seleccionar este Partido
                                </button>
                                <button type="button" 
                                        @click="closeModal()" 
                                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection