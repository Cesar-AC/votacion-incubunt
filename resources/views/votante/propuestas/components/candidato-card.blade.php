{{--
    Componente: Tarjeta de Candidato (Compact Card con Avatar Circular)
    Diseño: Clean, Modern, Mobile-First
    Props: candidato, area, compact (opcional)
--}}
@props(['candidato', 'area' => '', 'compact' => false])

@php
    $nombre = $candidato->usuario->perfil->nombre ?? 'Usuario';
    $apellido = $candidato->usuario->perfil->apellidoPaterno ?? '';
    $initials = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
    $carrera = $candidato->usuario->perfil->carrera->carrera ?? 'Sin carrera asignada';
    $foto = $candidato->usuario->perfil->fotoPerfil ?? null;
@endphp

<article onclick="openModal('candidato', {{ $candidato->idCandidato }}, '{{ $area }}')"
         class="candidato-card group bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 p-4 cursor-pointer transition-all duration-200 {{ $compact ? 'flex-shrink-0' : '' }}"
         @if($compact) style="width: 160px; min-width: 160px;" @endif>

    {{-- Avatar circular centrado --}}
    <div class="flex flex-col items-center text-center">
        {{-- Foto/Avatar --}}
        <div class="relative mb-3">
            @if($foto)
                <img src="{{ $foto }}"
                     alt="{{ $nombre }}"
                     class="w-16 h-16 rounded-full object-cover border-3 border-white shadow-md ring-2 ring-gray-100">
            @else
                {{-- Avatar con iniciales --}}
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xl font-bold shadow-md ring-2 ring-gray-100">
                    {{ $initials }}
                </div>
            @endif
        </div>

        {{-- Nombre --}}
        <h4 class="font-semibold text-gray-900 text-sm leading-tight mb-0.5 line-clamp-2 min-h-[2.5rem]">
            {{ $nombre }} {{ $apellido }}
        </h4>

        {{-- Carrera --}}
        <p class="text-xs text-gray-500 mb-3 line-clamp-1">
            {{ Str::limit($carrera, 20) }}
        </p>

        {{-- Botón Ver Perfil --}}
        <button class="w-full py-2 px-3 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 active:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-1 group-hover:text-indigo-600 group-hover:bg-indigo-50">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
            </svg>
            <span>Ver Perfil</span>
        </button>
    </div>

    {{-- Data oculta para el modal --}}
    <div id="data-candidato-{{ $candidato->idCandidato }}" class="hidden">
        <div class="nombre">{{ $nombre }} {{ $apellido }}</div>
        <div class="carrera">{{ $carrera }}</div>
        <div class="foto">{{ $foto ?? '' }}</div>
        <div class="initials">{{ $initials }}</div>
        <div class="desc">{{ $candidato->usuario->perfil->biografia ?? 'Candidato comprometido con el desarrollo de la organización.' }}</div>
        <div class="plan-url">{{ $candidato->planTrabajo ?? '' }}</div>
        <div class="propuestas">
            [
            @foreach($candidato->propuestas as $p)
                "{{ addslashes($p->descripcion) }}"{{ !$loop->last ? ',' : '' }}
            @endforeach
            ]
        </div>
    </div>
</article>
