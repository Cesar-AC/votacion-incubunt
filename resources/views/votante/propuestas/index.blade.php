@extends('layouts.admin')

@section('title', 'Propuestas - Candidatos 2026')

@push('styles')
<style>
    /* ========================================
       PROPUESTAS - MODERN MOBILE-FIRST DESIGN
       ======================================== */

    /* Ocultar scrollbar pero mantener funcionalidad */
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }

    /* Line clamp utilities */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Card transitions */
    .partido-card,
    .candidato-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    @media (min-width: 640px) {
        .partido-card:hover,
        .candidato-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }
    }

    @media (max-width: 639px) {
        .partido-card:active,
        .candidato-card:active {
            transform: scale(0.98);
        }
    }

    /* Scroll snap para móvil */
    .scroll-snap-x {
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }
    .scroll-snap-x > div > * {
        scroll-snap-align: start;
    }

    /* Scroll indicators */
    .scroll-dots {
        display: flex;
        justify-content: center;
        gap: 6px;
        padding: 8px 0;
    }
    .scroll-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #d1d5db;
        transition: all 0.3s ease;
    }
    .scroll-dot.active {
        background-color: #4f46e5;
        width: 18px;
        border-radius: 3px;
    }

    /* Section header clean style */
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .section-header-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Modal - Bottom Sheet en móvil */
    #modal-inner {
        transition: all 0.3s cubic-bezier(0.32, 0.72, 0, 1);
    }

    @media (max-width: 639px) {
        #modal-inner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-height: 90vh;
            border-radius: 24px 24px 0 0;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">

        {{-- Header minimalista --}}
        <div class="mb-6 sm:mb-8">
            <a href="{{ route('votante.home') }}"
               class="inline-flex items-center text-gray-500 hover:text-gray-900 font-medium transition-colors text-sm mb-4">
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Volver
            </a>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                Propuestas {{ date('Y') }}
            </h1>
            <p class="text-sm sm:text-base text-gray-500">
                Conoce las propuestas antes de votar
            </p>
        </div>

        {{-- Tip Card minimalista --}}
        <div class="mb-6 sm:mb-8 p-4 bg-blue-50 rounded-2xl border border-blue-100">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-sm text-blue-800">
                    Toca una tarjeta para ver detalles y propuestas
                </p>
            </div>
        </div>

        {{-- =============================================
             SECCIÓN: PARTIDOS POLÍTICOS
             ============================================= --}}
        @if($partidos->count() > 0)
        <section class="mb-8 sm:mb-10">
            {{-- Header de sección --}}
            <div class="section-header">
                <div class="section-header-icon bg-gradient-to-br from-indigo-500 to-purple-600">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Partidos Políticos</h2>
                    <p class="text-xs text-gray-500">{{ $partidos->count() }} {{ $partidos->count() == 1 ? 'partido' : 'partidos' }} en contienda</p>
                </div>
            </div>

            {{-- Grid/Scroll de partidos --}}
            <div class="scroll-indicator">
                <div id="partidos-scroll" class="overflow-x-auto hide-scroll -mx-4 px-4 sm:mx-0 sm:px-0 scroll-snap-x">
                    <div class="flex gap-4 pb-2" style="min-width: min-content;">
                        @forelse($partidos as $partido)
                            @include('votante.propuestas.components.partido-card', ['partido' => $partido])
                        @empty
                            <div class="w-full p-8 bg-white rounded-2xl shadow-sm text-center">
                                <p class="text-gray-400 text-sm">No hay partidos registrados</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if($partidos->count() > 1)
                <div id="partidos-dots" class="scroll-dots sm:hidden"></div>
                @endif
            </div>
        </section>

        {{-- Separador sutil --}}
        <div class="h-px bg-gray-200 my-6 sm:my-8"></div>
        @endif

        {{-- =============================================
             SECCIÓN: CANDIDATOS POR ÁREA
             ============================================= --}}
        @if($areas->count() > 0)
            @foreach($areas as $area)
                @php
                    $totalCandidatos = $area->cargos->sum(fn($cargo) => $cargo->candidatos->count());
                @endphp

                @if($totalCandidatos > 0)
                <section class="mb-8 sm:mb-10">
                    {{-- Header de área --}}
                    <div class="section-header">
                        <div class="section-header-icon bg-gradient-to-br from-gray-700 to-gray-900">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ $area->area }}</h2>
                            <p class="text-xs text-gray-500">{{ $totalCandidatos }} {{ $totalCandidatos == 1 ? 'candidato' : 'candidatos' }}</p>
                        </div>
                    </div>

                    {{-- Grid de candidatos --}}
                    @if($totalCandidatos >= 4)
                        {{-- Scroll horizontal para 4+ candidatos en móvil --}}
                        <div class="scroll-indicator">
                            <div id="area-{{ $area->idArea }}-scroll" class="overflow-x-auto hide-scroll -mx-4 px-4 sm:mx-0 sm:px-0 scroll-snap-x sm:overflow-visible">
                                <div class="flex gap-3 sm:grid sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 pb-2 sm:pb-0" style="min-width: min-content;">
                                    @foreach($area->cargos as $cargo)
                                        @foreach($cargo->candidatos as $candidato)
                                            @include('votante.propuestas.components.candidato-card', ['candidato' => $candidato, 'area' => $area->area, 'compact' => true])
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                            <div id="area-{{ $area->idArea }}-dots" class="scroll-dots sm:hidden"></div>
                        </div>
                    @else
                        {{-- Grid para pocos candidatos --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($area->cargos as $cargo)
                                @foreach($cargo->candidatos as $candidato)
                                    @include('votante.propuestas.components.candidato-card', ['candidato' => $candidato, 'area' => $area->area, 'compact' => true])
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </section>
                @endif
            @endforeach
        @else
            <div class="text-center py-12 px-6 bg-white rounded-2xl shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
                <p class="text-gray-500">No hay áreas configuradas</p>
            </div>
        @endif

    </div>
</div>

{{-- =============================================
     MODAL DE DETALLES - Bottom Sheet en móvil
     ============================================= --}}
<div id="detail-modal"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-end sm:items-center justify-center"
     style="z-index: 9999;"
     onclick="if(event.target === this) closeModal()">

    <div class="bg-white w-full sm:max-w-lg max-h-[90vh] sm:max-h-[85vh] overflow-hidden flex flex-col transform transition-all duration-300 translate-y-full sm:translate-y-0 sm:scale-95 opacity-0 rounded-t-3xl sm:rounded-2xl shadow-2xl"
         id="modal-inner"
         onclick="event.stopPropagation()">

        {{-- Handle bar para móvil --}}
        <div class="sm:hidden flex justify-center pt-3 pb-2">
            <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
        </div>

        {{-- Header del Modal --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Detalles</h3>
            <button onclick="closeModal()"
                    class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- Contenido del Modal --}}
        <div class="overflow-y-auto flex-1 px-5 py-4" id="modal-content">
            {{-- Contenido dinámico --}}
        </div>

        {{-- Footer sticky con botón descarga --}}
        <div id="modal-footer" class="px-5 py-4 border-t border-gray-100 bg-white" style="display: none;">
            <a id="download-plan-btn" href="#" target="_blank" download class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                </svg>
                <span>Descargar Plan de Trabajo</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/votante/propuestas.js') }}"></script>
@endpush

@endsection



