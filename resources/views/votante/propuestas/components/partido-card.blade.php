{{--
    Componente: Tarjeta de Partido (Hero Card)
    Diseño: Clean, Modern, Mobile-First
--}}
@props(['partido'])

<article @click="verPartidoModal = true; $store.modalPartido.partido = partido"
        class="partido-card group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden cursor-pointer transition-all duration-300 flex-shrink-0"
        style="width: 280px;"
        x-data="{
            partido: {
                'id': '{{ $partido->getKey() }}',
                'foto': '',
                'nombre': '{{ $partido->partido }}',
                'url': '{{ $partido->urlPartido }}',
                'descripcion': '{{ $partido->descripcion }}',
                'planTrabajo': '{{ $partido->planTrabajo }}',
                'candidatos': [
                    @foreach($partido->candidatos as $candidato)
                        {
                            'id': '{{ $candidato->getKey() }}',
                            'foto': '',
                            'nombre': '{{ $candidato->usuario->perfil->nombre }} {{ $candidato->usuario->perfil->otrosNombres }} {{ $candidato->usuario->perfil->apellidoPaterno }} {{ $candidato->usuario->perfil->apellidoMaterno }}',
                            'cargo': '{{ $candidatoService->obtenerCargoDeCandidatoEnElecciones($candidato, $eleccionActiva)->cargo }}',
                        },
                    @endforeach
                ],
                'propuestas': [
                    @foreach($partido->propuestas as $propuesta)
                        {
                            'descripcion': '{{ $propuesta->descripcion }}'
                        },
                    @endforeach
                ],
            }
        }">

    {{-- Header con indicador de color --}}
    <div class="relative">
        {{-- Barra de color indicadora --}}
        <div class="h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600"></div>

        {{-- Contenido del header --}}
        <div class="p-5 flex flex-col items-center gap-4">
            {{-- Logo/Icono del partido --}}
            <div class="max-w-24 rounded-2xl bg-gray-200 flex items-center justify-center shadow-lg shadow-indigo-200">
                <img src="{{ $partido->foto }}" alt="Foto de partido {{ $partido->partido }}" class="w-full h-full object-cover rounded-2xl">
            </div>

            {{-- Nombre del partido --}}
            <h3 class="text-lg font-bold text-gray-900 line-clamp-2 text-center leading-snug">
                {{ $partido->partido }}
            </h3>

            {{-- Avatares de miembros --}}
            <div class="flex items-center">
                <div class="flex -space-x-2">
                    @foreach($partido->candidatos->take(4) as $index => $candidato)
                        <div class="w-8 h-8 rounded-full border-2 border-white flex items-center justify-center text-white text-xs font-bold shadow-sm">
                            <img src="{{ $candidato->foto }}" alt="Foto de candidato {{ $candidato->nombre }}" class="w-full h-full object-cover rounded-full">
                        </div>
                    @endforeach
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
</article>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('modalPartido', {
        partido: {
            nombre: '',
            url: '',
            descripcion: '',
            planTrabajo: '',
            candidatos: [],
            propuestas: [],
        }
    })
})
</script>
@endpush

