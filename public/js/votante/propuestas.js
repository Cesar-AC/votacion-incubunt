// ================================
// MODAL FUNCTIONS
// ================================

/**
 * Actualizar footer del modal con botón de descarga
 */
function updateModalFooter(planUrl) {
    const footer = document.getElementById('modal-footer');
    const downloadBtn = document.getElementById('download-plan-btn');

    if (planUrl && planUrl.trim() !== '') {
        downloadBtn.href = planUrl;
        footer.style.display = 'block';
    } else {
        footer.style.display = 'none';
    }
}

/**
 * Cambiar contenido del modal sin cerrarlo (para navegación entre modales)
 */
function switchToModal(tipo, id, area = '') {
    const content = document.getElementById('modal-content');
    const dataElem = document.getElementById(`data-${tipo}-${id}`);

    if (!dataElem) return;

    // Scroll al inicio del modal
    content.scrollTop = 0;

    // Fade out rápido
    content.style.opacity = '0';
    content.style.transition = 'opacity 0.15s ease-out';

    setTimeout(() => {
        // Cargar nuevo contenido usando la misma lógica que openModal
        loadModalContent(tipo, id, area);

        // Fade in
        setTimeout(() => {
            content.style.opacity = '1';
        }, 50);
    }, 150);
}

/**
 * Cargar contenido del modal (compartido entre openModal y switchToModal)
 */
function loadModalContent(tipo, id, area = '') {
    const content = document.getElementById('modal-content');
    const dataElem = document.getElementById(`data-${tipo}-${id}`);

    if (!dataElem) return;

    let html = '';

    if (tipo === 'candidato') {
        const nombre = dataElem.querySelector('.nombre').textContent;
        const carrera = dataElem.querySelector('.carrera').textContent;
        const foto = dataElem.querySelector('.foto').textContent;
        const initials = dataElem.querySelector('.initials')?.textContent || nombre.substring(0,2).toUpperCase();
        const desc = dataElem.querySelector('.desc').textContent;
        const planUrl = dataElem.querySelector('.plan-url')?.textContent || '';

        let propuestas = [];
        try {
            propuestas = JSON.parse(dataElem.querySelector('.propuestas').textContent);
        } catch(e) { console.error('Error parsing propuestas:', e); }

        const propuestasHTML = propuestas.length > 0
            ? propuestas.map(p => `
                <li class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center mt-0.5">
                        <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700 leading-relaxed">${p}</span>
                </li>
            `).join('')
            : '<li class="p-4 text-center text-gray-400 italic text-sm">No hay propuestas registradas.</li>';

        // Avatar HTML con fallback a iniciales
        const avatarHTML = foto
            ? `<img src="${foto}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-xl" alt="${nombre}">`
            : `<div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-white shadow-xl">${initials}</div>`;

        html = `
            <!-- Header con Avatar -->
            <div class="flex flex-col items-center text-center pb-6 border-b border-gray-100">
                <div class="mb-4">
                    ${avatarHTML}
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">${nombre}</h2>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        ${carrera}
                    </span>
                    ${area ? `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">${area}</span>` : ''}
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
                    <p class="text-sm text-gray-700 leading-relaxed">${desc}</p>
                </div>
            </div>

            <!-- Propuestas -->
            <div class="py-5">
                <h3 class="flex items-center gap-2 text-base font-bold text-gray-900 mb-4">
                    <div class="w-1 h-5 bg-indigo-600 rounded-full"></div>
                    Propuestas
                </h3>
                <ul class="space-y-2">
                    ${propuestasHTML}
                </ul>
            </div>
        `;

        // Mostrar footer con botón descarga si hay URL
        updateModalFooter(planUrl);
    } else {
        // PARTIDO
        const nombre = dataElem.querySelector('.nombre').textContent;
        const lema = dataElem.querySelector('.lema').textContent;
        const desc = dataElem.querySelector('.desc').textContent;
        const planUrl = dataElem.querySelector('.plan-url')?.textContent || '';

        let miembros = [], propuestas = [];
        try {
            miembros = JSON.parse(dataElem.querySelector('.miembros').textContent.trim());
            propuestas = JSON.parse(dataElem.querySelector('.propuestas').textContent.trim());
        } catch(e) { console.error('Error parsing JSON:', e); }

        const miembrosHTML = miembros.map(m => {
            const initials = m.nombre.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase();
            return `
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 cursor-pointer transition-colors"
                 onclick="event.stopPropagation(); switchToModal('candidato', ${m.id}, '');">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    ${initials}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">${m.nombre}</p>
                    <p class="text-xs text-gray-500 truncate">${m.rol}</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        `}).join('');

        const propuestasHTML = propuestas.length > 0
            ? propuestas.map(p => `
                <li class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center mt-0.5">
                        <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700 leading-relaxed">${p}</span>
                </li>
            `).join('')
            : '<li class="p-4 text-center text-gray-400 italic text-sm">No hay propuestas registradas.</li>';

        html = `
            <!-- Header del Partido -->
            <div class="flex flex-col items-center text-center pb-6 border-b border-gray-100">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">${nombre}</h2>
                <p class="text-sm text-gray-500 italic">"${lema}"</p>
            </div>

            <!-- Descripción -->
            <div class="py-5 border-b border-gray-100">
                <p class="text-sm text-gray-600 leading-relaxed text-center">${desc}</p>
            </div>

            <!-- Miembros del Equipo -->
            <div class="py-5 border-b border-gray-100">
                <h3 class="flex items-center gap-2 text-base font-bold text-gray-900 mb-4">
                    <div class="w-1 h-5 bg-purple-600 rounded-full"></div>
                    Miembros del Equipo
                </h3>
                <div class="space-y-2">
                    ${miembrosHTML}
                </div>
            </div>

            <!-- Propuestas -->
            <div class="py-5">
                <h3 class="flex items-center gap-2 text-base font-bold text-gray-900 mb-4">
                    <div class="w-1 h-5 bg-indigo-600 rounded-full"></div>
                    Propuestas del Partido
                </h3>
                <ul class="space-y-2">
                    ${propuestasHTML}
                </ul>
            </div>
        `;

        // Mostrar footer con botón descarga si hay URL
        updateModalFooter(planUrl);
    }

    content.innerHTML = html;
}

function openModal(tipo, id, area = '') {
    const modal = document.getElementById('detail-modal');
    const modalInner = document.getElementById('modal-inner');

    // Cargar contenido del modal
    loadModalContent(tipo, id, area);

    // Mostrar modal
    modal.classList.remove('hidden');

    // Animación de entrada (Bottom Sheet en móvil, Scale en desktop)
    requestAnimationFrame(() => {
        modalInner.classList.remove('translate-y-full', 'sm:scale-95', 'opacity-0');
        modalInner.classList.add('translate-y-0', 'sm:scale-100', 'opacity-100');
    });
}

function closeModal() {
    const modal = document.getElementById('detail-modal');
    const modalInner = document.getElementById('modal-inner');

    // Animación de salida
    modalInner.classList.remove('translate-y-0', 'sm:scale-100', 'opacity-100');
    modalInner.classList.add('translate-y-full', 'sm:scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}


// ================================
// SCROLL INDICATORS
// ================================

function initScrollIndicators() {
    // Helper function
    function setupScrollIndicators(scrollId, dotsId) {
        const scrollContainer = document.getElementById(scrollId);
        const dotsContainer = document.getElementById(dotsId);

        if (!scrollContainer || !dotsContainer) return;

        const cards = scrollContainer.querySelectorAll('.partido-card, .candidato-card');
        const cardsCount = cards.length;

        if (cardsCount <= 1) {
            dotsContainer.style.display = 'none';
            return;
        }

        // Crear dots
        dotsContainer.innerHTML = '';
        for (let i = 0; i < cardsCount; i++) {
            const dot = document.createElement('div');
            dot.className = 'scroll-dot' + (i === 0 ? ' active' : '');
            dotsContainer.appendChild(dot);
        }

        // Actualizar dots en scroll
        scrollContainer.addEventListener('scroll', function() {
            const scrollLeft = this.scrollLeft;
            const cardWidth = cards[0].offsetWidth + 12;
            const activeIndex = Math.round(scrollLeft / cardWidth);

            dotsContainer.querySelectorAll('.scroll-dot').forEach((dot, idx) => {
                dot.classList.toggle('active', idx === activeIndex);
            });
        }, { passive: true });
    }

    // Indicadores para partidos
    setupScrollIndicators('partidos-scroll', 'partidos-dots');

    // Indicadores para áreas
    document.querySelectorAll('[id^="area-"][id$="-scroll"]').forEach(scrollElem => {
        const match = scrollElem.id.match(/area-(\d+)-scroll/);
        if (match) {
            setupScrollIndicators(`area-${match[1]}-scroll`, `area-${match[1]}-dots`);
        }
    });
}


// ================================
// INITIALIZATION
// ================================

document.addEventListener('DOMContentLoaded', function() {
    initScrollIndicators();

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    // Prevenir scroll del body cuando modal está abierto
    const modal = document.getElementById('detail-modal');
    if (modal) {
        const observer = new MutationObserver(function(mutations) {
            document.body.style.overflow = modal.classList.contains('hidden') ? '' : 'hidden';
        });
        observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
    }
});
