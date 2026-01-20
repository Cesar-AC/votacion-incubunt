@php
    $isAdmin = Auth::check() && Auth::user()->roles->where('rol', 'administrador')->count() > 0;
@endphp

<!-- Bottom Navigation (visible en todas las pantallas) -->
<nav class="navbar fixed-bottom navbar-light bg-white border-top shadow-lg" 
     style="z-index: 1030; height: 65px;"
     role="navigation"
     aria-label="Navegación principal">
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-around align-items-center w-100" style="height: 65px;">
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="text-decoration-none d-flex flex-column align-items-center justify-content-center nav-item-bottom"
               style="width: 25%; height: 100%;">
                @php $activeDashboard = request()->routeIs('dashboard'); @endphp
                <i class="fas fa-tachometer-alt" 
                   style="font-size: 1.3rem; color: {{ $activeDashboard ? '#161349' : '#6c757d' }};"></i>
                <span class="text-xs mt-1" style="color: {{ $activeDashboard ? '#161349' : '#6c757d' }}; font-size: 0.7rem;">
                    Dashboard
                </span>
            </a>

            <!-- Portal de Votación -->
            <a href="{{ route('votante.home') }}" 
               class="text-decoration-none d-flex flex-column align-items-center justify-content-center nav-item-bottom"
               style="width: 25%; height: 100%;">
                @php $activeHome = request()->routeIs('votante.home'); @endphp
                <i class="fas fa-home" 
                   style="font-size: 1.3rem; color: {{ $activeHome ? '#161349' : '#6c757d' }};"></i>
                <span class="text-xs mt-1" style="color: {{ $activeHome ? '#161349' : '#6c757d' }}; font-size: 0.7rem;">
                    Portal
                </span>
            </a>

            <!-- Elecciones (botón central destacado) -->
            <a href="{{ route('votante.elecciones') }}" 
               class="text-decoration-none d-flex flex-column align-items-center justify-content-center position-relative nav-item-bottom"
               style="width: 25%; height: 100%; margin-top: -25px;">
                @php $activeElecciones = request()->routeIs('votante.elecciones*') || request()->routeIs('votante.votar.*'); @endphp
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-lg central-button" 
                     style="width: 56px; height: 56px; background-color: {{ $activeElecciones ? '#ffb700' : '#161349' }}; border: 4px solid white;">
                    <i class="fas fa-vote-yea text-white" style="font-size: 1.4rem;"></i>
                </div>
                <span class="text-xs mt-1" style="color: {{ $activeElecciones ? '#ffb700' : '#161349' }}; font-size: 0.7rem; font-weight: 600;">
                    Votar
                </span>
            </a>

            <!-- Menú (hamburguesa) -->
            <a href="#" 
               class="text-decoration-none d-flex flex-column align-items-center justify-content-center nav-item-bottom"
               style="width: 25%; height: 100%;"
               data-toggle="modal" 
               data-target="#mobileMenuModal">
                <i class="fas fa-bars" style="font-size: 1.3rem; color: #6c757d;"></i>
                <span class="text-xs mt-1" style="color: #6c757d; font-size: 0.7rem;">
                    Menú
                </span>
            </a>
        </div>
    </div>
</nav>

<!-- Modal de Menú -->
<div class="modal fade" id="mobileMenuModal" tabindex="-1" role="dialog" aria-labelledby="mobileMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 rounded-lg shadow-lg">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #161349 0%, #3a2c99 100%);">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center" id="mobileMenuModalLabel">
                    <i class="fas fa-bars mr-2"></i> Menú Principal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush">
                    
                    <!-- Sección Votación (Siempre visible) -->
                    <div class="list-group-item bg-light font-weight-bold text-uppercase small px-3 py-2" 
                         style="color: #161349; background-color: #f8f9fa !important;">
                        <i class="fas fa-vote-yea mr-2"></i>Votación
                    </div>
                    <a href="{{ route('votante.home') }}" 
                       class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                       data-dismiss="modal">
                        <i class="fas fa-home mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                        <span>Portal Votante</span>
                    </a>
                    <a href="{{ route('votante.elecciones') }}" 
                       class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                       data-dismiss="modal">
                        <i class="fas fa-vote-yea mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                        <span>Ver Elecciones</span>
                    </a>
                    
                    @if($isAdmin)
                        <!-- Sección Admin -->
                        <div class="list-group-item bg-light font-weight-bold text-uppercase small px-3 py-2" 
                             style="color: #161349; background-color: #f8f9fa !important;">
                            <i class="fas fa-user-shield mr-2"></i>Gestión de Elecciones
                        </div>
                        <a href="{{ route('crud.elecciones.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-vote-yea mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                            <span>Gestionar Elecciones</span>
                        </a>
                        <a href="{{ route('crud.padron_electoral.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-clipboard-list mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                            <span>Padrones</span>
                        </a>
                        <a href="{{ route('crud.lista_votante.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-list-ol mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                            <span>Listas Votantes</span>
                        </a>
                        <a href="{{ route('crud.voto.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-box mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                            <span>Votos</span>
                        </a>

                        <!-- Usuarios -->
                        <div class="list-group-item bg-light font-weight-bold text-uppercase small px-3 py-2" 
                             style="color: #161349; background-color: #f8f9fa !important;">
                            <i class="fas fa-users mr-2"></i>Usuarios
                        </div>
                        <a href="{{ route('crud.user.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-users mr-3" style="color: #17a2b8; font-size: 1.1rem;"></i>
                            <span>Usuarios</span>
                        </a>
                        <a href="{{ route('crud.permiso.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-key mr-3" style="color: #17a2b8; font-size: 1.1rem;"></i>
                            <span>Permisos</span>
                        </a>

                        <!-- Carreras -->
                        <a href="{{ route('crud.carrera.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-graduation-cap mr-3" style="color: #6c757d; font-size: 1.1rem;"></i>
                            <span>Carreras</span>
                        </a>

                        <!-- Cargos -->
                        <a href="{{ route('crud.cargo.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-briefcase mr-3" style="color: #6c757d; font-size: 1.1rem;"></i>
                            <span>Cargos</span>
                        </a>

                        <!-- Partidos -->
                        <div class="list-group-item bg-light font-weight-bold text-uppercase small px-3 py-2" 
                             style="color: #161349; background-color: #f8f9fa !important;">
                            <i class="fas fa-flag mr-2"></i>Partidos
                        </div>
                        <a href="{{ route('crud.partido.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-flag mr-3" style="color: #FFB700; font-size: 1.1rem;"></i>
                            <span>Partidos</span>
                        </a>
                        <a href="{{ route('crud.candidato.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-user-tie mr-3" style="color: #FFB700; font-size: 1.1rem;"></i>
                            <span>Candidatos</span>
                        </a>
                        <a href="{{ route('crud.propuesta_candidato.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-file-alt mr-3" style="color: #6c757d; font-size: 1.1rem;"></i>
                            <span>Propuestas Candidatos</span>
                        </a>
                        <a href="{{ route('crud.propuesta_partido.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-file-contract mr-3" style="color: #6c757d; font-size: 1.1rem;"></i>
                            <span>Propuestas Partidos</span>
                        </a>
                        
                        <!-- Configuración -->
                        <div class="list-group-item bg-light font-weight-bold text-uppercase small px-3 py-2" 
                             style="color: #161349; background-color: #f8f9fa !important;">
                            <i class="fas fa-cog mr-2"></i>Configuración
                        </div>
                        <a href="{{ route('crud.area.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-shapes mr-3" style="color: #6c757d; font-size: 1.1rem;"></i>
                            <span>Áreas y Carreras</span>
                        </a>
                        <a href="{{ route('crud.rol.ver') }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 pl-4 border-0 menu-item"
                           data-dismiss="modal">
                            <i class="fas fa-user-tag mr-3" style="color: #6c757d; font-size: 1.1rem;"></i>
                            <span>Roles</span>
                        </a>
                    @else
                        <!-- Opciones para usuarios normales -->
                        <div class="list-group-item bg-light font-weight-bold text-uppercase small px-3 py-2" 
                             style="color: #161349; background-color: #f8f9fa !important;">
                            <i class="fas fa-user mr-2"></i>Mi Cuenta
                        </div>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0 menu-item">
                            <i class="fas fa-user mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0 menu-item">
                            <i class="fas fa-cog mr-3" style="color: #161349; font-size: 1.1rem;"></i>
                            <span>Configuración</span>
                        </a>
                    @endif

                    <!-- Cerrar Sesión -->
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" 
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0 mt-2 w-100 text-left"
                           style="background-color: #fff5f5; color: #dc3545; font-weight: 600; border: none; cursor: pointer;">
                            <i class="fas fa-sign-out-alt mr-3" style="font-size: 1.1rem;"></i>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ocultar sidebar vertical completamente en todas las pantallas */
#accordionSidebar {
    display: none !important;
}

/* Asegurar que el content wrapper no tenga margin */
#content-wrapper {
    margin-left: 0 !important;
    padding-bottom: 80px !important;
}

/* Ocultar el topbar toggle button */
#sidebarToggleTop {
    display: none !important;
}

/* Estilos para el bottom nav */
.navbar.fixed-bottom {
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

/* Interactividad del bottom nav */
.nav-item-bottom:active {
    transform: scale(0.95);
    transition: transform 0.1s;
}

/* Animación del botón central */
.central-button {
    transition: all 0.3s ease;
}

.nav-item-bottom:active .central-button {
    transform: scale(0.9);
}

/* Mejorar hover en modal */
.menu-item:hover {
    background-color: #f8f9fa !important;
    transform: translateX(5px);
    transition: all 0.2s ease;
}

/* Animación suave para el modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

/* Responsive adjustments */
@media (min-width: 768px) {
    /* En pantallas más grandes, podemos aumentar el tamaño del modal */
    #mobileMenuModal .modal-dialog {
        max-width: 500px;
    }
}

@media (max-width: 576px) {
    /* En pantallas muy pequeñas, usar todo el ancho */
    #mobileMenuModal .modal-dialog {
        max-width: 95%;
        margin: 0.5rem auto;
    }
}

/* Estilos para el botón de cerrar sesión */
.list-group-item button[type="submit"]:hover {
    background-color: #ffe5e5 !important;
}

/* Accesibilidad - focus visible */
.nav-item-bottom:focus,
.menu-item:focus {
    outline: 2px solid #161349;
    outline-offset: 2px;
}
</style>