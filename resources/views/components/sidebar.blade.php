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
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Menu Principal
        </div>

        <!-- Nav Item - Gestionar Elecciones Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGestion"
                aria-expanded="false" aria-controls="collapseGestion">
                <i class="fas fa-fw fa-tasks"></i>
                <span>Gestionar Elecciones</span>
            </a>
            <div id="collapseGestion" class="collapse" aria-labelledby="headingGestion">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header" style="color: black !important;">Opciones:</h6>
                    <a class="collapse-item" href="{{ route('crud.elecciones.ver') }}" style="color: black !important;">Gestionar Elecciones</a>
                    <a class="collapse-item" href="{{ route('crud.padron_electoral.ver') }}" style="color: black !important;">Gestionar Padrones</a>
                    <a class="collapse-item" href="{{ route('crud.user.ver') }}" style="color: black !important;">Gestionar Usuarios</a>
                    <a class="collapse-item" href="{{ route('crud.partido.ver') }}" style="color: black !important;">Gestionar Partidos</a>
                    <a class="collapse-item" href="#" style="color: black !important;">Configuraciones</a>
                </div>
            </div>
        </li>

        <!-- Nav Item - Reportes -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Reportes</span></a>
        </li>
    @else
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Menu Principal
        </div>

        <!-- Nav Item - Votaciones -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('crud.elecciones.ver') }}">
                <i class="fas fa-fw fa-vote-yea"></i>
                <span>Votaciones</span></a>
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