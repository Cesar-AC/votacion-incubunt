<div id="modal-container" class="hidden">
    <div id="info-candidato-modal-container" class="fixed top-0 left-0 h-screen w-screen flex sm:grid sm:grid-cols-7 sm:grid-rows-5 items-center justify-center bg-gray-300/50 z-50">
        <div id="info-candidato-modal" class="relative sm:col-start-2 sm:row-start-2 sm:col-span-5 sm:row-span-3 p-2">
            <span id="info-candidato-modal-close" class="absolute top-0 right-0 p-2 fas fa-times mr-4 mt-4 p-2 cursor-pointer" onclick="closeModal2('modal-container')"></span>
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <!-- Header con Avatar -->
                <div class="flex flex-col items-center text-center pb-6 border-b border-gray-100">
                    <div class="mb-4">
                        <div id="info-candidato-modal-avatar" class="">
                            <img id="info-candidato-modal-avatar-img" src="" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-xl" alt="">
                        </div>
                        <div id="info-candidato-modal-avatar-initials" class="hidden">
                            <div id="info-candidato-modal-avatar-initials-text" class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-white shadow-xl">
                                <span id="info-candidato-modal-avatar-initials-text-span">IN</span>
                            </div>
                        </div>
                    </div>
                    <h2 id="info-candidato-modal-nombre" class="text-xl font-bold text-gray-900 mb-2">Nombre</h2>
                    <div class="flex flex-wrap justify-center gap-2">
                        <span id="info-candidato-modal-carrera" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            Carrera
                        </span>

                        <span id="info-candidato-modal-area" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Area</span>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="py-5 border-b border-gray-100">
                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p id="info-candidato-modal-descripcion" class="text-sm text-gray-700 leading-relaxed">Descripción</p>
                    </div>
                </div>

                <!-- Propuestas -->
                <div class="p-4 max-h-40 flex flex-col gap-5 overflow-y-auto overflow-x-clip md:grid md:grid-cols-2 md:gap-4 break-words">
                    <div>
                        <h3 id="info-candidato-modal-propuestas-generales" class="flex items-center gap-2 text-base font-bold text-gray-900 mb-4">
                            <div class="w-1 h-5 bg-indigo-600 rounded-full"></div>
                            Propuestas generales
                        </h3>
                        <ul id="info-candidato-modal-propuestas-generales-lista" class="space-y-2">

                        </ul>
                    </div>

                    <div class="">
                        <h3 id="info-candidato-modal-propuestas-area" class="flex items-center gap-2 text-base font-bold text-gray-900 mb-4 md:justify-end">
                            <div class="md:hidden w-1 h-5 bg-indigo-600 rounded-full"></div>
                            Propuestas de área
                            <div class="hidden md:block w-1 h-5 bg-indigo-600 rounded-full"></div>
                        </h3>
                        <ul id="info-candidato-modal-propuestas-area-lista" class="space-y-2 md:text-end">
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function generarPropuestas(propuestas, listaID, alineacion = 'izquierda') {
            if (propuestas.length === 0) {
                var lista = document.getElementById(listaID);
                lista.innerHTML = `
                    <li class="flex items-center justify-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-700 leading-relaxed">No hay propuestas registradas.</span>
                    </li>`;
                return;
            }

            var docFragment = document.createDocumentFragment();
            var lista = document.getElementById(listaID);
            lista.innerHTML = '';

            if (alineacion === 'izquierda') {
                liClass = 'flex items-start gap-3 p-3 bg-gray-50 rounded-xl';
            } else {
                liClass = 'flex md:flex-row-reverse items-start gap-3 p-3 bg-gray-50 rounded-xl';
            }
            
            for (propuesta of propuestas) {
                var li = document.createElement('li');
                li.className = liClass;
                li.innerHTML = `
                    <span class="fas fa-check-circle text-indigo-600"></span>
                    <div class="flex flex-col">
                        <p class="font-bold text-gray-900">${propuesta.propuesta}</p>
                        <span class="text-sm text-gray-700 leading-relaxed">${propuesta.descripcion}</span>
                    </div>
                `;
                docFragment.appendChild(li);
            }
            lista.appendChild(docFragment);
        }

        function closeModal2(modalContainerID) {
            document.getElementById(modalContainerID).classList.add('hidden');
        }

        function openModal2(modalContainerID, candidatoID) {
            document.getElementById(modalContainerID).classList.remove('hidden');

            var datosCandidato = document.getElementById('data-candidato-' + candidatoID);
            var nombre = datosCandidato.getAttribute('data-nombre');
            var carrera = datosCandidato.getAttribute('data-carrera');
            var area = datosCandidato.getAttribute('data-area');
            var descripcion = datosCandidato.getAttribute('data-descripcion');
            var propuestasGenerales = JSON.parse(datosCandidato.getAttribute('data-propuestas-generales'));
            var propuestasArea = JSON.parse(datosCandidato.getAttribute('data-propuestas-area'));
            var foto = datosCandidato.getAttribute('data-foto');

            document.getElementById('info-candidato-modal-nombre').textContent = nombre;
            document.getElementById('info-candidato-modal-carrera').textContent = carrera;
            document.getElementById('info-candidato-modal-area').textContent = area;
            document.getElementById('info-candidato-modal-descripcion').textContent = descripcion;

            generarPropuestas(propuestasGenerales, 'info-candidato-modal-propuestas-generales-lista');
            generarPropuestas(propuestasArea, 'info-candidato-modal-propuestas-area-lista', 'derecha');
        }
    </script>
@endpush