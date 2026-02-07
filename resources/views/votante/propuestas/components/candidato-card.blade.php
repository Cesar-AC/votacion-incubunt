@props(['candidato', 'area' => '', 'compact' => false])

@php
    $nombre = join(' ', [$candidato->usuario->perfil->nombre, $candidato->usuario->perfil->otrosNombres, $candidato->usuario->perfil->apellidoPaterno, $candidato->usuario->perfil->apellidoMaterno]);
    $apellido = $candidato->usuario->perfil->apellidoPaterno ?? '';
    $initials = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
    $carrera = $candidato->usuario->perfil->carrera->carrera ?? 'Sin carrera asignada';
    $foto = $candidato->usuario->perfil->fotoPerfil ?? null;
@endphp

<article @click="verCandidatoModal = true; $store.modalCandidato.candidato = candidato"
         class="partido-card group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden cursor-pointer transition-all duration-300 flex-shrink-0"
         style="width: 280px; min-width: 280px;"
         x-data="{
            candidato: {
                'id': '{{ $candidato->getKey() }}',
                'nombre': '{{ $nombre }}',
                'fotoURL': '',
                'carrera': '{{ $candidato->usuario->perfil->carrera->carrera }}',
                'area': '{{ $area }}',
                'cargo': '{{ $candidatoService->obtenerCargoDeCandidatoEnElecciones($candidato, $eleccionActiva)->cargo }}',
                'planTrabajoURL': '',
                'propuestas': [
                    @foreach($candidato->propuestas as $propuesta)
                        {
                            'titulo': '{{ $propuesta->propuesta }}',
                            'descripcion': '{{ $propuesta->descripcion }}'
                        },
                    @endforeach
                ],
            }
        }">

    {{-- Header con indicador de color --}}
    <div class="relative">
        {{-- Barra de color indicadora --}}
        <div class="h-1.5 bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500"></div>

        {{-- Contenido del header --}}
        <div class="px-5 py-3 flex flex-col items-center gap-4">
            <h3 class="text-lg text-gray-900 line-clamp-2 text-center leading-snug py-2">
                <span class="text-gray-500 font-normal text-sm">Para el cargo de</span>
                <span class="font-bold text-lg text-amber-500">{{ $candidatoService->obtenerCargoDeCandidatoEnElecciones($candidato, $eleccionActiva)->cargo }}</span>
            </h3>

            {{-- Logo/Icono del Candidato --}}
            <div class="max-w-24 rounded-2xl bg-gray-200 flex items-center justify-center shadow-lg shadow-indigo-200">
                <img src="{{ $candidato->foto }}" alt="Foto de candidato {{ $nombre }}" class="w-full h-full object-cover rounded-2xl">
            </div>

            {{-- Nombre del Candidato --}}
            <h4 class="text-lg font-bold text-gray-900 line-clamp-2 text-center leading-snug">
                {{ $nombre }}
            </h4>
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
    Alpine.store('modalCandidato', {
        candidato: {
            id: '',
            nombre: '',
            fotoURL: '',
            carrera: '',
            area: '',
            cargo: '',
            planTrabajoURL: '',
            propuestas: [],
        }
    })
})
</script>
@endpush
