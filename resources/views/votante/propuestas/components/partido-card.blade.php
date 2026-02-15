{{--
    Componente: Tarjeta de Partido (Hero Card)
    Diseño: Clean, Modern, Mobile-First
--}}
@props(['partido'])

@php
    $partidoFotoURL = $partido->obtenerFotoURL() ?? null;
@endphp

<article @click="verPartidoModal = true; setPartidoModal({{ $partido->getKey() }})"
        class="partido-card group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden cursor-pointer transition-all duration-300 flex-shrink-0"
        style="width: 280px;"
        x-data>

    {{-- Header con indicador de color --}}
    <div class="relative">
        {{-- Barra de color indicadora --}}
        <div class="h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600"></div>

        {{-- Contenido del header --}}
        <div class="p-5 flex flex-col items-center gap-4">
            {{-- Logo/Icono del partido - solo si existe foto --}}
            @if($partidoFotoURL)
            <div class="w-24 h-24 rounded-2xl bg-gray-200 flex items-center justify-center shadow-lg shadow-indigo-200 flex-shrink-0 overflow-hidden">
                <img src="{{ $partidoFotoURL }}" alt="Foto de partido {{ $partido->partido }}" class="w-full h-full object-cover rounded-2xl">
            </div>
            @else
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-200 flex-shrink-0">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"/>
                </svg>
            </div>
            @endif

            {{-- Nombre del partido --}}
            <h3 class="text-lg font-bold text-gray-900 line-clamp-2 text-center leading-snug">
                {{ $partido->partido }}
            </h3>

            {{-- Descripción breve --}}
            <p class="text-sm text-gray-500 line-clamp-2 mb-4 min-h-[2.5rem]">
                {{ Str::limit($partido->descripcion ?? 'Conoce nuestras propuestas y plan de trabajo.', 80) }}
            </p>
        </div>
    </div>

    {{-- Footer con botón --}}
    <div class="px-5 pb-5">
        <button class="w-full h-11 bg-gray-900 hover:bg-gray-800 active:bg-black text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 text-sm group-hover:bg-indigo-600">
            <span>Ver Propuestas</span>
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    {{-- Hidden data for modal population --}}
    <div class="hidden" data-partido-id="{{ $partido->getKey() }}" 
         data-partido-nombre="{{ $partido->partido }}"
         data-partido-url="{{ $partido->urlPartido }}"
         data-partido-descripcion="{{ $partido->descripcion }}"
         data-partido-plan-trabajo="{{ $partido->planTrabajo }}"
         data-partido-foto="{{ $partidoFotoURL ?? '' }}">
    </div>
</article>

