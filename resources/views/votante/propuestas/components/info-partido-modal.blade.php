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
                        <!-- Logo Box -->
                        <div class="flex-shrink-0">
                            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-none flex items-center justify-center bg-white p-2">
                                <img id="partido-modal-logo" x-bind:src="$store.modalPartido.partido.foto" alt="Partido Logo" class="w-full h-full object-contain">
                            </div>
                        </div>

                        <!-- Text Info -->
                        <div class="flex-1 space-y-3 pt-2">
                            <h2 id="partido-modal-nombre" class="text-3xl font-black text-gray-900 uppercase tracking-tight leading-none" x-text="$store.modalPartido.partido.nombre"></h2>
                            <div class="h-1 w-20 bg-indigo-600 my-2"></div>
                            <p id="partido-modal-descripcion" class="text-gray-600 leading-relaxed text-sm md:text-base max-w-2xl text-justify" x-text="$store.modalPartido.partido.descripcion"></p>
                            <div class="pt-2">
                                <a id="partido-modal-enlace" href="#" target="_blank" class="inline-flex items-center gap-2 text-indigo-600 font-bold hover:text-indigo-800 hover:underline transition-colors group">
                                    <span class="group-hover:translate-x-1 transition-transform" x-text="$store.modalPartido.partido.url"></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0 w-full lg:w-auto mt-4 lg:mt-0">
                        <a target="_blank" id="partido-modal-btn-descarga" x-bind:href="$store.modalPartido.partido.planTrabajo" class="block w-full lg:w-64 text-black p-6 text-center text-md">
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
                    <div id="partido-modal-candidatos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12 px-4 max-w-5xl mx-auto">
                        <template x-for="candidato in $store.modalPartido.partido.candidatos" :key="candidato.id">
                            <div class="w-full bg-white rounded-[2.5rem] p-6 lg:p-8 flex flex-col items-center relative overflow-hidden h-full"> 
                                <div class="w-32 h-24 mb-5 overflow-hidden bg-gray-100">
                                    <img x-bind:src="candidato.foto" class="w-full h-full object-cover" alt="Candidato foto">
                                </div>

                                <div class="flex flex-col items-center gap-4">
                                    <h3 class="text-xl font-black text-indigo-600 uppercase tracking-widest text-center" x-text="candidato.cargo"></h3>

                                    <h4 class="text-xl text-gray-900 text-center uppercase leading-tight" x-text="candidato.nombre"></h4>

                                    <button class="w-full py-3 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-indigo-600 transition-colors">
                                        Ver Detalles
                                    </button>
                                </div>
                            </div>
                        </template> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>