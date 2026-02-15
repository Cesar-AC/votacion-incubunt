<div id="partido-modal-container" x-cloak x-show="verPartidoModal">
    <div id="info-partido-modal-container" class="fixed top-0 left-0 h-screen w-screen flex items-center justify-center bg-gray-900/60 z-[60] backdrop-blur-sm overflow-hidden">
        <!-- Modal Card -->
        <div class="relative bg-white w-full max-w-6xl mx-4 my-auto max-h-[90vh] flex flex-col rounded-3xl shadow-2xl overflow-hidden" @click.outside="verPartidoModal = false">
            
            <!-- Close Button -->
            <button @click="verPartidoModal = false" id="partido-modal-close" class="absolute top-5 right-5 z-20 w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-900 transition-all focus:outline-none">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto p-8 sm:p-10 custom-scrollbar">
                <!-- HEADER SECTION -->
                <div class="flex flex-col lg:flex-row justify-between items-start gap-8 mb-12 border-b border-gray-100 pb-10">
                    <!-- Left: Logo & Info -->
                    <div class="flex flex-col sm:flex-row gap-6 md:gap-8 flex-1 items-center text-center">
                        <!-- Logo Box - solo si existe foto -->
                        <div class="flex-shrink-0" id="partido-modal-logo-container">
                            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-none flex items-center justify-center bg-white p-2 border border-gray-100">
                                <img id="partido-modal-logo" src="" alt="Partido Logo" class="w-full h-full object-contain">
                            </div>
                        </div>

                        <!-- Text Info -->
                        <div class="flex-1 space-y-3 pt-2">
                            <h2 id="partido-modal-nombre" class="text-3xl font-black text-gray-900 uppercase tracking-tight leading-none">Nombre Partido</h2>
                            <div class="h-1 w-20 bg-indigo-600 my-2"></div>
                            <p id="partido-modal-descripcion" class="text-gray-600 leading-relaxed text-sm md:text-base max-w-2xl text-justify">Descripción</p>
                            <div class="pt-2">
                                <a id="partido-modal-enlace" href="#" target="_blank" class="inline-flex items-center gap-2 text-indigo-600 font-bold hover:text-indigo-800 hover:underline transition-colors group">
                                    <span class="group-hover:translate-x-1 transition-transform" id="partido-modal-enlace-text">www.ejemplo.com</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0 w-full lg:w-auto mt-4 lg:mt-0">
                        <a target="_blank" id="partido-modal-btn-descarga" href="#" class="block w-full lg:w-64 text-black p-6 text-center text-md bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors" style="text-decoration: none;">
                            <span class="fas fa-file-pdf"></span>
                            <span class="block font-bold leading-tight">Ver plan de trabajo</span>
                        </a>
                    </div>
                </div>

                <!-- CANDIDATES SECTION -->
                <div class="mb-8">
                    <div class="flex items-center justify-center mb-10">
                        <h3 class="text-center text-sm font-black text-gray-900 uppercase tracking-[0.2em] relative px-4">
                            <span class="bg-white relative z-10 px-4">Candidatos por este partido</span>
                            <div class="absolute inset-0 top-1/2 h-px bg-gray-200 -z-0"></div>
                        </h3>
                    </div>

                    <!-- Candidates Grid -->
                    <div id="partido-modal-candidatos-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 max-w-4xl mx-auto">
                    </div>
                </div>

                <!-- PROPOSTAS SECTION -->
                <div class="mb-8">
                    <div class="flex items-center justify-center mb-10">
                        <h3 class="text-center text-sm font-black text-gray-900 uppercase tracking-[0.2em] relative px-4">
                            <span class="bg-white relative z-10 px-4">Propuestas</span>
                            <div class="absolute inset-0 top-1/2 h-px bg-gray-200 -z-0"></div>
                        </h3>
                    </div>

                    <!-- Propuestas List -->
                    <div id="partido-modal-propuestas-lista" class="max-w-3xl mx-auto space-y-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function setPartidoModal(partidoId) {
            // Obtener el componente Alpine
            const pagina = document.querySelector('#pagina-propuestas');
            const alpineData = Alpine.$data(pagina);
            
            // Abrir modal
            alpineData.verPartidoModal = true;

            // Obtener datos del partido desde data attributes
            const partidoCards = document.querySelectorAll('[data-partido-id]');
            let partidoData = null;

            for (let card of partidoCards) {
                if (card.getAttribute('data-partido-id') === String(partidoId)) {
                    partidoData = {
                        id: card.getAttribute('data-partido-id'),
                        nombre: card.getAttribute('data-partido-nombre'),
                        url: card.getAttribute('data-partido-url'),
                        descripcion: card.getAttribute('data-partido-descripcion'),
                        planTrabajo: card.getAttribute('data-partido-plan-trabajo'),
                        foto: card.getAttribute('data-partido-foto'),
                    };
                    break;
                }
            }

            if (!partidoData) return;

            // Actualizar elementos del modal
            document.getElementById('partido-modal-nombre').textContent = partidoData.nombre;
            document.getElementById('partido-modal-descripcion').textContent = partidoData.descripcion;
            
            const enlaceElem = document.getElementById('partido-modal-enlace');
            enlaceElem.href = partidoData.url;
            document.getElementById('partido-modal-enlace-text').textContent = partidoData.url;

            const downloadBtn = document.getElementById('partido-modal-btn-descarga');
            downloadBtn.href = partidoData.planTrabajo;

            // Mostrar/ocultar logo según si existe
            const logoContainer = document.getElementById('partido-modal-logo-container');
            const logoImg = document.getElementById('partido-modal-logo');
            
            if (partidoData.foto) {
                logoImg.src = partidoData.foto;
                logoContainer.style.display = 'block';
            } else {
                logoContainer.style.display = 'none';
            }

            // Cargar candidatos presidenciales
            cargarCandidatosPartido(partidoId);

            // Cargar propuestas del partido
            cargarPropuestasPartido(partidoId);
        }

        function cargarCandidatosPartido(partidoId) {
            // Obtener candidatos presidenciales del partido
            const rutaBase = '{{ route("votante.propuestas.candidatos", ":partidoId") }}';
            const ruta = rutaBase.replace(':partidoId', partidoId);
            
            fetch(ruta)
                .then(response => response.json())
                .then(data => {
                    const grid = document.getElementById('partido-modal-candidatos-grid');
                    grid.innerHTML = '';

                    if (data.length === 0) {
                        grid.innerHTML = '<p class="text-gray-500">No hay candidatos registrados</p>';
                        return;
                    }

                    data.forEach(candidato => {
                        const candidatoHTML = document.createElement('div');
                        candidatoHTML.className = 'flex items-center gap-4 bg-white rounded-2xl p-4 border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all cursor-pointer';
                        candidatoHTML.onclick = function() {
                            document.dispatchEvent(new CustomEvent('abrirCandidatoModal', {detail: {candidatoId: candidato.id}}));
                        };
                        
                        let fotoHTML = '';
                        if (candidato.foto) {
                            fotoHTML = `<img src="${candidato.foto}" class="w-full h-full object-cover rounded-full" alt="Candidato foto">`;
                        } else {
                            fotoHTML = `<div class="w-full h-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xl font-bold rounded-full">${candidato.initials}</div>`;
                        }

                        candidatoHTML.innerHTML = `
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 overflow-hidden">
                                    ${fotoHTML}
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1">${candidato.cargo}</p>
                                <h4 class="text-base font-bold text-gray-900 truncate mb-1">${candidato.nombre}</h4>
                                <p class="text-xs text-gray-500">${candidato.carrera || ''}</p>
                            </div>
                        `;
                        grid.appendChild(candidatoHTML);
                    });
                })
                .catch(error => console.error('Error cargando candidatos:', error));
        }

        function cargarPropuestasPartido(partidoId) {
            const rutaBase = '{{ route("votante.propuestas.partido.propuestas", ":partidoId") }}';
            const ruta = rutaBase.replace(':partidoId', partidoId);
            
            fetch(ruta)
                .then(response => response.json())
                .then(data => {
                    const lista = document.getElementById('partido-modal-propuestas-lista');
                    lista.innerHTML = '';

                    if (data.length === 0) {
                        lista.innerHTML = '<p class="text-gray-500 text-center">No hay propuestas registradas</p>';
                        return;
                    }

                    data.forEach(propuesta => {
                        const li = document.createElement('li');
                        li.className = 'flex items-start gap-3 p-4 bg-blue-50 rounded-lg border border-blue-100';
                        li.innerHTML = `
                            <span class="fas fa-lightbulb text-blue-600 mt-1 flex-shrink-0"></span>
                            <div class="flex flex-col">
                                <p class="font-bold text-gray-900">${propuesta.propuesta || 'Propuesta'}</p>
                                <span class="text-sm text-gray-700 leading-relaxed">${propuesta.descripcion}</span>
                            </div>
                        `;
                        lista.appendChild(li);
                    });
                })
                .catch(error => console.error('Error cargando propuestas:', error));
        }
    </script>
@endpush