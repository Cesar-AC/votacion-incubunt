<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos 2026 - Incubunt VOTE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .animate-delay-1 {
            animation-delay: 0.1s;
            animation-fill-mode: both;
        }
        .animate-delay-2 {
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 min-h-screen">
    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8 max-w-7xl">
        
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-800 to-blue-700 rounded-2xl p-6 sm:p-8 mb-8 shadow-2xl animate-fade-in" role="banner">
            <div class="flex items-center gap-4">
                <div class="bg-amber-500 w-14 h-14 sm:w-16 sm:h-16 rounded-full flex items-center justify-center font-bold text-xl sm:text-2xl text-blue-900 shadow-lg" aria-hidden="true">
                    ING
                </div>
                <h1 class="text-white text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight">
                    incubunt <span class="font-normal">VOTE</span>
                </h1>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-800 to-blue-700 rounded-2xl p-8 sm:p-12 lg:p-16 mb-10 shadow-2xl animate-fade-in animate-delay-1" aria-labelledby="hero-title">
            <h2 id="hero-title" class="text-white text-4xl sm:text-5xl lg:text-6xl font-bold mb-4 leading-tight">
                Candidatos 2026
            </h2>
            <p class="text-blue-100 text-lg sm:text-xl lg:text-2xl">
                Conoce a quienes liderarán Incubunt.
            </p>
        </section>

        <!-- Section Title -->
        <div class="mb-8 animate-fade-in animate-delay-2">
            <h3 class="text-blue-200 text-sm sm:text-base font-bold tracking-widest uppercase px-2">
                Postulación Presidencial
            </h3>
        </div>
        
        <!-- Cards Container -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-12">
            
            <!-- Card 1: Partido A - Sinergia (Blue) -->
            <article class="bg-white rounded-2xl overflow-hidden shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 transition-all duration-300 animate-fade-in animate-delay-2" aria-labelledby="partido-a-title">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-500 p-8 sm:p-10">
                    <h3 id="partido-a-title" class="text-white text-3xl sm:text-4xl font-bold mb-2">
                        Partido A - Sinergia
                    </h3>
                    <p class="text-blue-50 text-lg sm:text-xl italic font-light">
                        "Innovación y Liderazgo"
                    </p>
                </div>
                
                <!-- Card Body -->
                <div class="p-6 sm:p-8">
                    <!-- Avatar Group -->
                    <div class="flex mb-6" role="group" aria-label="Miembros del equipo">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-300 border-4 border-white shadow-md" aria-label="Miembro 1"></div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-300 border-4 border-white shadow-md -ml-4" aria-label="Miembro 2"></div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-300 border-4 border-white shadow-md -ml-4" aria-label="Miembro 3"></div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-700 border-4 border-white shadow-md -ml-4 flex items-center justify-center text-white font-bold text-sm sm:text-base" aria-label="3 miembros más">
                            +3
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-gray-700 text-base sm:text-lg leading-relaxed mb-6">
                        Somos un equipo multidisciplinario de la UNT comprometidos con potenciar el ecosistema emprendedor. Creemos en la fuerza de la unión entre facultades para crear líderes integrales.
                    </p>
                    
                    <!-- Link -->
                    <a href="#" 
                       class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-bold text-base sm:text-lg transition-all duration-300 hover:gap-4 focus:outline-none focus:ring-4 focus:ring-blue-300 rounded-lg px-2 py-1"
                       aria-label="Ver equipo y propuestas de Partido A - Sinergia">
                        <span>Ver equipo y propuestas</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Card 2: Partido B - Impulso (Orange) -->
            <article class="bg-white rounded-2xl overflow-hidden shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 transition-all duration-300 animate-fade-in animate-delay-2" aria-labelledby="partido-b-title">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-amber-500 to-amber-400 p-8 sm:p-10">
                    <h3 id="partido-b-title" class="text-gray-900 text-3xl sm:text-4xl font-bold mb-2">
                        Partido B - Impulso
                    </h3>
                    <p class="text-gray-800 text-lg sm:text-xl italic font-light">
                        "Acción que Transforma"
                    </p>
                </div>
                
                <!-- Card Body -->
                <div class="p-6 sm:p-8">
                    <!-- Avatar Group -->
                    <div class="flex mb-6" role="group" aria-label="Miembros del equipo">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-300 border-4 border-white shadow-md" aria-label="Miembro 1"></div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-300 border-4 border-white shadow-md -ml-4" aria-label="Miembro 2"></div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-300 border-4 border-white shadow-md -ml-4" aria-label="Miembro 3"></div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-700 border-4 border-white shadow-md -ml-4 flex items-center justify-center text-white font-bold text-sm sm:text-base" aria-label="3 miembros más">
                            +3
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-gray-700 text-base sm:text-lg leading-relaxed mb-6">
                        Buscamos transformar incubunt en un referente nacional comprometido con la innovación y el emprendimiento estudiantil de impacto.
                    </p>
                    
                    <!-- Link -->
                    <a href="#" 
                       class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-800 font-bold text-base sm:text-lg transition-all duration-300 hover:gap-4 focus:outline-none focus:ring-4 focus:ring-amber-300 rounded-lg px-2 py-1"
                       aria-label="Ver equipo y propuestas de Partido B - Impulso">
                        <span>Ver equipo y propuestas</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </article>
        </div>

        <!-- Footer -->
        <footer class="text-center py-8">
            <p class="text-blue-200 text-sm">
                &copy; 2026 Incubunt VOTE - Sistema de Votación Electoral
            </p>
        </footer>
    </div>
</body>
</html>
