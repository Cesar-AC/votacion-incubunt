@php
    $isAdmin = Auth::check() && Auth::user()->roles->where('rol', 'administrador')->count() > 0;
@endphp

<ul class="navbar-nav sidebar sidebar-dark" id="accordionSidebar" style="background-color: #161349;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class=" sidebar-brand-icon">
            <img src="{{ asset('img/VOTAINCUBI.png') }}" width="40" height="40"/>
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    @if($isAdmin)
        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Menu Principal
        </div>

        <!-- GESTIÓN DE ELECCIONES -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseElecciones"
                aria-expanded="false" aria-controls="collapseElecciones">
                <i class="fas fa-fw fa-vote-yea"></i>
                <span>Gestión de Elecciones</span>
            </a>
            <div id="collapseElecciones" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header text-dark">Opciones:</h6>
                    <a class="collapse-item text-dark" href="{{ route('crud.elecciones.ver') }}">Elecciones</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.padron_electoral.ver') }}">Padrones</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.voto.ver') }}">Votos</a>
                </div>
            </div>
        </li>

        <!-- GESTIÓN DE USUARIOS -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsuarios"
                aria-expanded="false" aria-controls="collapseUsuarios">
                <i class="fas fa-fw fa-users"></i>
                <span>Gestión de Usuarios</span>
            </a>
            <div id="collapseUsuarios" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header text-dark">Opciones:</h6>
                    <a class="collapse-item text-dark" href="{{ route('crud.user.ver') }}">Usuarios</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.permiso.ver') }}">Permisos</a>
                </div>
            </div>
        </li>

        <!-- GESTIÓN DE PARTIDOS -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePartidos"
                aria-expanded="false" aria-controls="collapsePartidos">
                <i class="fas fa-fw fa-flag"></i>
                <span>Gestión de Partidos</span>
            </a>
            <div id="collapsePartidos" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header text-dark">Opciones:</h6>
                    <a class="collapse-item text-dark" href="{{ route('crud.candidato.ver') }}">Candidatos</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.propuesta_candidato.ver') }}">Propuestas Candidato</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.partido.ver') }}">Partidos</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.propuesta_partido.ver') }}">Propuestas Partido</a>
                </div>
            </div>
        </li>

        <!-- CONFIGURACIÓN -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig"
                aria-expanded="false" aria-controls="collapseConfig">
                <i class="fas fa-fw fa-cogs"></i>
                <span>Configuración</span>
            </a>
            <div id="collapseConfig" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header text-dark">Opciones:</h6>
                    <a class="collapse-item text-dark" href="{{ route('crud.area.ver') }}">Áreas</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.candidato.ver') }}">Candidatos</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.cargo.ver') }}">Cargos</a>
                    <a class="collapse-item text-dark" href="{{ route('crud.carrera.ver') }}">Carreras</a>
                </div>
            </div>
        </li>

        <!-- REPORTES -->
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Reportes</span>
            </a>
        </li>
    @endif


    <!-- Nav Item - Historiales -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-history"></i>
            <span>Historiales</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->