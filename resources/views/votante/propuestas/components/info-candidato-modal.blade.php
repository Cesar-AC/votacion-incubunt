<div x-cloak x-show="verCandidatoModal" id="modal-container">
    <div id="info-candidato-modal-container" class="fixed top-0 left-0 h-screen w-screen flex sm:grid sm:grid-cols-7 sm:grid-rows-5 items-center justify-center bg-gray-300/50 z-50 p-4">
        <div id="info-candidato-modal" class="relative sm:col-start-2 sm:row-start-2 sm:col-span-5 sm:row-span-3 w-full max-w-full" @click.outside="verCandidatoModal = false">
            <span id="info-candidato-modal-close" class="absolute top-2 right-2 sm:top-4 sm:right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 cursor-pointer z-10 text-gray-600" @click="verCandidatoModal = false">
                <i class="fas fa-times"></i>
            </span>
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 lg:p-8">
                <!-- Header con Avatar -->
                <div class="flex flex-col items-center text-center pb-4 sm:pb-6 border-b border-gray-100">
                    <div class="mb-3 sm:mb-4">
                        <div id="info-candidato-modal-avatar" class="">
                            <img id="info-candidato-modal-avatar-img" src="" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-white shadow-xl" alt="">
                        </div>
                        <div id="info-candidato-modal-avatar-initials" class="hidden">
                            <div id="info-candidato-modal-avatar-initials-text" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xl sm:text-2xl font-bold border-4 border-white shadow-xl">
                                <span id="info-candidato-modal-avatar-initials-text-span">IN</span>
                            </div>
                        </div>
                    </div>
                    <h2 id="info-candidato-modal-nombre" class="text-lg sm:text-xl font-bold text-gray-900 mb-2 break-words px-2 leading-tight">Nombre</h2>
                    <div class="flex flex-wrap justify-center gap-2">
                        <span id="info-candidato-modal-carrera" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            Carrera
                        </span>

                        <span id="info-candidato-modal-area" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Area</span>

                        <span id="info-candidato-modal-cargo" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Cargo</span>
                    </div>
                </div>

                <!-- Plan de Trabajo Link -->
                <div id="plan-trabajo-container" class="py-3 sm:py-5 border-b border-gray-100 hidden">
                    <a id="info-candidato-modal-plan-trabajo" href="#" target="_blank" class="flex items-start gap-2 sm:gap-3 p-3 sm:p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors cursor-pointer">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0113 3.414V4h3a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2v-1h-1a2 2 0 01-2-2V4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-green-900 text-sm sm:text-base">Plan de trabajo</p>
                            <p class="text-xs sm:text-sm text-green-700">Descargar documento PDF</p>
                        </div>
                    </a>
                </div>

                <!-- Propuestas -->
                <div class="p-2 sm:p-4 max-h-60 sm:max-h-80 flex flex-col gap-3 sm:gap-5 overflow-y-auto overflow-x-clip md:grid md:grid-cols-1 md:gap-4 break-words">
                    <div id="propuestas-container">
                        <h3 id="propuestas-titulo" class="flex items-center gap-2 text-sm sm:text-base font-bold text-gray-900 mb-3 sm:mb-4">
                            <div class="w-1 h-5 bg-indigo-600 rounded-full"></div>
                            Propuestas
                        </h3>
                        <ul id="info-candidato-modal-propuestas-lista" class="space-y-2">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function setCandidatoModal(candidatoId) {
            // Obtener el componente Alpine
            const pagina = document.querySelector('#pagina-propuestas');
            const alpineData = Alpine.$data(pagina);

            // Obtener datos del candidato desde data attributes
            const candidatoCards = document.querySelectorAll('[data-candidato-id]');
            let candidatoData = null;

            for (let card of candidatoCards) {
                if (card.getAttribute('data-candidato-id') === String(candidatoId)) {
                    candidatoData = {
                        id: card.getAttribute('data-candidato-id'),
                        nombre: card.getAttribute('data-candidato-nombre'),
                        carrera: card.getAttribute('data-candidato-carrera'),
                        area: card.getAttribute('data-candidato-area'),
                        cargo: card.getAttribute('data-candidato-cargo'),
                        foto: card.getAttribute('data-candidato-foto'),
                        planTrabajo: card.getAttribute('data-candidato-plan-trabajo'),
                        esPresidencial: card.getAttribute('data-candidato-es-presidencial') === 'true'
                    };
                    break;
                }
            }

            if (!candidatoData) return;

            // Abrir modal
            alpineData.verCandidatoModal = true;

            // Actualizar elementos del modal
            document.getElementById('info-candidato-modal-nombre').textContent = candidatoData.nombre;
            document.getElementById('info-candidato-modal-carrera').textContent = candidatoData.carrera;
            document.getElementById('info-candidato-modal-area').textContent = candidatoData.area;
            document.getElementById('info-candidato-modal-cargo').textContent = candidatoData.cargo;

            // Mostrar/ocultar avatar
            const avatarImg = document.getElementById('info-candidato-modal-avatar-img');
            const avatarInitials = document.getElementById('info-candidato-modal-avatar-initials');
            const avatarContainer = document.getElementById('info-candidato-modal-avatar');

            if (candidatoData.foto) {
                avatarImg.src = candidatoData.foto;
                avatarContainer.style.display = 'block';
                avatarInitials.style.display = 'none';
            } else {
                avatarContainer.style.display = 'none';
                avatarInitials.style.display = 'block';
                const primerLetra = candidatoData.nombre.charAt(0).toUpperCase();
                const apellidoLetra = candidatoData.nombre.split(' ').pop().charAt(0).toUpperCase();
                document.getElementById('info-candidato-modal-avatar-initials-text-span').textContent = primerLetra + apellidoLetra;
            }

            // Mostrar/ocultar plan de trabajo
            const planTrabajoContainer = document.getElementById('plan-trabajo-container');
            if (candidatoData.planTrabajo && candidatoData.planTrabajo !== '' && !candidatoData.esPresidencial) {
                planTrabajoContainer.classList.remove('hidden');
                document.getElementById('info-candidato-modal-plan-trabajo').href = candidatoData.planTrabajo;
            } else {
                planTrabajoContainer.classList.add('hidden');
            }

            // Cargar propuestas
            cargarPropuestasCandidato(candidatoId, candidatoData.esPresidencial);
        }

        function cargarPropuestasCandidato(candidatoId, esPresidencial) {
            let ruta;
            if (esPresidencial) {
                const rutaBase = '{{ route("votante.propuestas.candidato.propuestas-partido", ":candidatoId") }}';
                ruta = rutaBase.replace(':candidatoId', candidatoId);
            } else {
                const rutaBase = '{{ route("votante.propuestas.candidato.propuestas", ":candidatoId") }}';
                ruta = rutaBase.replace(':candidatoId', candidatoId);
            }

            fetch(ruta)
                .then(response => response.json())
                .then(data => {
                    const lista = document.getElementById('info-candidato-modal-propuestas-lista');
                    const titulo = document.getElementById('propuestas-titulo');
                    
                    lista.innerHTML = '';

                    if (data.length === 0) {
                        lista.innerHTML = `
                            <li class="flex items-center justify-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm text-gray-700 leading-relaxed">No hay propuestas registradas.</span>
                            </li>`;
                        return;
                    }

                    data.forEach(propuesta => {
                        const li = document.createElement('li');
                        li.className = 'flex items-start gap-2 sm:gap-3 p-2 sm:p-3 bg-gray-50 rounded-xl';
                        li.innerHTML = `
                            <span class="fas fa-check-circle text-indigo-600 mt-1 flex-shrink-0 text-sm"></span>
                            <div class="flex flex-col break-words w-full">
                                <p class="font-bold text-gray-900 text-sm sm:text-base break-words">${propuesta.propuesta || propuesta.titulo || 'Propuesta'}</p>
                                <span class="text-xs sm:text-sm text-gray-700 leading-relaxed break-words">${propuesta.descripcion}</span>
                            </div>
                        `;
                        lista.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Error cargando propuestas:', error);
                    const lista = document.getElementById('info-candidato-modal-propuestas-lista');
                    lista.innerHTML = `
                        <li class="flex items-center justify-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm text-gray-700 leading-relaxed">Error cargando propuestas.</span>
                        </li>`;
                });
        }
    </script>
@endpush