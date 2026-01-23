{{--
    Componente: Tarjeta de Partido (Hero Card)
    Diseño: Clean, Modern, Mobile-First
--}}
@props(['partido'])

<article onclick="openModal('partido', {{ $partido->idPartido }})"
         class="partido-card group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden cursor-pointer transition-all duration-300 flex-shrink-0"
         style="width: 280px; min-width: 280px;">

    {{-- Header con indicador de color --}}
    <div class="relative">
        {{-- Barra de color indicadora --}}
        <div class="h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600"></div>

        {{-- Contenido del header --}}
        <div class="p-5">
            {{-- Logo/Icono del partido --}}
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"/>
                    </svg>
                </div>
            </div>

            {{-- Nombre del partido --}}
            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 leading-snug">
                {{ $partido->partido }}
            </h3>

            {{-- Avatares de miembros --}}
            <div class="flex items-center mb-4">
                <div class="flex -space-x-2">
                    @foreach($partido->candidatos->take(4) as $index => $candidato)
                        @php
                            $initials = strtoupper(substr($candidato->usuario->perfil->nombre ?? 'U', 0, 1) . substr($candidato->usuario->perfil->apellidoPaterno ?? '', 0, 1));
                            $colors = ['bg-indigo-500', 'bg-purple-500', 'bg-pink-500', 'bg-blue-500'];
                            $bgColor = $colors[$index % count($colors)];
                        @endphp
                        <div class="w-8 h-8 rounded-full {{ $bgColor }} border-2 border-white flex items-center justify-center text-white text-xs font-bold shadow-sm">
                            {{ $initials }}
                        </div>
                    @endforeach
                    @if($partido->candidatos->count() > 4)
                        <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-gray-600 text-xs font-bold">
                            +{{ $partido->candidatos->count() - 4 }}
                        </div>
                    @endif
                </div>
            </div>

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

    {{-- Data oculta para el modal --}}
    <div id="data-partido-{{ $partido->idPartido }}" class="hidden">
        <div class="nombre">{{ $partido->partido }}</div>
        <div class="lema">Juntos construyendo el futuro</div>
        <div class="desc">{{ $partido->descripcion ?? 'Sin descripción disponible.' }}</div>
        <div class="plan-url">{{ $partido->planTrabajo ?? '' }}</div>
        <div class="miembros">
            [
            @foreach($partido->candidatos as $c)
                {"id": "{{ $c->idCandidato }}", "rol": "{{ $c->cargo->cargo ?? 'Miembro' }}", "nombre": "{{ $c->usuario->perfil->nombre }} {{ $c->usuario->perfil->apellidoPaterno }}", "carrera": "{{ $c->usuario->perfil->carrera->carrera ?? 'Sin carrera asignada' }}", "foto": "{{ $c->usuario->perfil->fotoPerfil ?? '' }}"}{{ !$loop->last ? ',' : '' }}
            @endforeach
            ]
        </div>
        <div class="propuestas">
            [
            @foreach($partido->propuestas as $p)
                "{{ addslashes($p->descripcion) }}"{{ !$loop->last ? ',' : '' }}
            @endforeach
            ]
        </div>
    </div>
</article>
