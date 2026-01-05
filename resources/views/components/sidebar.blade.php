@php
    $isAdmin = Auth::check() && Auth::user()->roles->where('rol', 'administrador')->count() > 0;

@endphp

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #161349; transition: all 0.3s;">

    <a class="sidebar-brand d-flex align-items-center justify-content-center py-3" href="{{ route('dashboard') }}" aria-label="Ir al Inicio de VotaIncubi">
      <svg xmlns="http://www.w3.org/2000/svg" width="200" height="60" viewBox="0 0 150 45" preserveAspectRatio="xMidYMid meet">
  <path fill="#3a2c99" d="M 25.1 17.6 L 123.5 17.6 L 123.5 44.1 L 25.1 44.1 Z" />
  
  <path fill="#c8c7ee" d="M 64.3 17.6 L 25.1 17.6 L 33.2 8.9 L 56.1 8.9 Z" />
  
  <path fill="#3a2c99" d="M 55.1 14.3 L 34.3 14.3 C 33.7 14.3 33.3 13.8 33.3 13.2 C 33.3 12.7 33.7 12.2 34.3 12.2 L 55.1 12.2 C 55.6 12.2 56.1 12.7 56.1 13.2 C 56.1 13.8 55.6 14.3 55.1 14.3 Z" />
  
  <path fill="#ffb700" d="M 37.1 0 L 52.3 0 L 52.3 14.2 L 37.1 14.2 Z" />
  
  <circle cx="44.7" cy="7" r="4.3" fill="#ffffff" />
  <path fill="#3a2c99" d="M 44.6 9.3 C 44.3 9.3 44.1 9.2 43.9 9 L 42.6 7.7 C 42.2 7.3 42.2 6.6 42.6 6.2 C 43 5.8 43.6 5.8 44 6.2 L 44.6 6.8 L 47.9 3.5 C 48.3 3.1 48.9 3.1 49.3 3.5 C 49.7 3.9 49.7 4.6 49.3 5 L 45.3 9 C 45.1 9.2 44.9 9.3 44.6 9.3 Z" />

<rect x="30" y="24.3" width="90" height="13.1" fill="#ffffff" />
  <g transform="matrix(1.1, 0, 0, 1.2, 36.5, 35)"> 
      <path fill="#000" d="M 2.78 -7.87 L 0.14 -7.87 L 3.15 0 L 5.68 0 L 8.71 -7.87 L 6.09 -7.87 L 4.42 -3.43 Z"/>
      <path fill="#000" d="M 12.5 0.12 C 11.3 0.12 10.4 -0.22 9.7 -0.93 C 9.1 -1.64 8.8 -2.64 8.8 -3.93 C 8.8 -5.23 9.1 -6.23 9.7 -6.93 C 10.4 -7.64 11.3 -8 12.5 -8 C 13.7 -8 14.6 -7.64 15.2 -6.93 C 15.8 -6.23 16.2 -5.23 16.2 -3.93 C 16.2 -2.64 15.8 -1.64 15.2 -0.93 C 14.6 -0.22 13.7 0.12 12.5 0.12 Z M 12.5 -2.09 C 12.8 -2.09 13.1 -2.25 13.3 -2.56 C 13.5 -2.88 13.6 -3.34 13.6 -3.93 C 13.6 -4.53 13.5 -4.98 13.3 -5.31 C 13.1 -5.63 12.8 -5.79 12.5 -5.79 C 12.1 -5.79 11.8 -5.63 11.6 -5.31 C 11.4 -4.98 11.3 -4.53 11.3 -3.93 C 11.3 -3.34 11.4 -2.88 11.6 -2.56 C 11.8 -2.25 12.1 -2.09 12.5 -2.09 Z"/>
      <path fill="#000" d="M 23.7 -5.67 L 21.6 -5.67 L 21.6 0 L 19.0 0 L 19.0 -5.67 L 16.9 -5.67 L 16.9 -7.87 L 23.7 -7.87 Z"/>
      <path fill="#000" d="M 28.3 -1.15 L 25.6 -1.15 L 25.2 0 L 22.7 0 L 25.7 -7.87 L 28.3 -7.87 L 31.2 0 L 28.7 0 Z M 27.6 -3.03 L 27.0 -5.18 L 26.3 -3.03 Z"/>
      <path fill="#000" d="M 34.3 -7.87 L 34.3 0 L 31.8 0 L 31.8 -7.87 Z"/>
      <path fill="#000" d="M 35.3 0 L 35.3 -7.89 L 37.5 -7.89 L 39.6 -4.23 L 39.6 -7.87 L 42.2 -7.87 L 42.2 0 L 40.0 -0.01 L 37.9 -3.65 L 37.9 0 Z"/>
      <path fill="#000" d="M 47.6 -3.28 C 47.5 -2.48 47.1 -2.09 46.6 -2.09 C 46.3 -2.09 46.0 -2.25 45.8 -2.56 C 45.6 -2.88 45.6 -3.34 45.6 -3.93 C 45.6 -4.53 45.6 -4.98 45.8 -5.31 C 46.0 -5.63 46.3 -5.79 46.6 -5.79 C 46.8 -5.79 47.0 -5.68 47.2 -5.46 C 47.4 -5.25 47.5 -4.96 47.6 -4.57 L 50.0 -4.96 C 49.8 -5.93 49.4 -6.67 48.8 -7.20 C 48.2 -7.73 47.5 -8 46.6 -8 C 45.5 -8 44.6 -7.64 44.0 -6.93 C 43.3 -6.23 43.0 -5.23 43.0 -3.93 C 43.0 -2.64 43.3 -1.64 44.0 -0.93 C 44.6 -0.22 45.5 0.12 46.6 0.12 C 47.5 0.12 48.2 -0.14 48.8 -0.67 C 49.4 -1.20 49.8 -1.94 50.0 -2.89 Z"/>
      <path fill="#000" d="M 51.0 -7.87 L 53.5 -7.87 L 53.5 -3.54 C 53.5 -3.07 53.6 -2.71 53.8 -2.46 C 53.9 -2.21 54.1 -2.09 54.4 -2.09 C 54.7 -2.09 54.9 -2.21 55.0 -2.46 C 55.2 -2.71 55.3 -3.07 55.3 -3.54 L 55.3 -7.87 L 57.8 -7.87 L 57.8 -3.54 C 57.8 -2.37 57.5 -1.47 56.9 -0.82 C 56.3 -0.19 55.5 0.12 54.4 0.12 C 53.3 0.12 52.5 -0.19 51.9 -0.82 C 51.3 -1.47 51.0 -2.37 51.0 -3.54 Z"/>
      <path fill="#000" d="M 64.5 -4.07 C 65.4 -3.77 65.9 -3.15 65.9 -2.21 C 65.9 -1.51 65.6 -0.97 65.0 -0.57 C 64.4 -0.19 63.6 0 62.5 0 L 58.8 0 L 58.8 -7.87 L 62.9 -7.87 C 63.7 -7.87 64.4 -7.67 64.8 -7.29 C 65.2 -6.91 65.4 -6.36 65.4 -5.65 C 65.4 -4.90 65.1 -4.37 64.5 -4.07 Z M 61.3 -4.89 L 62.5 -4.89 C 62.9 -4.89 63.1 -5.04 63.1 -5.35 C 63.1 -5.67 62.9 -5.85 62.5 -5.90 L 61.3 -5.90 Z M 62.7 -1.96 C 63.2 -1.96 63.4 -2.14 63.4 -2.51 C 63.4 -2.89 63.2 -3.07 62.7 -3.07 L 61.3 -3.07 L 61.3 -1.96 Z"/>
      <path fill="#000" d="M 69.3 -7.87 L 69.3 0 L 66.8 0 L 66.8 -7.87 Z"/>
  </g>
</svg>
    </a>

  <hr class="sidebar-divider my-0 border-white/10">

    <li class="nav-item">
        @php $active = request()->routeIs('dashboard'); @endphp
        <a class="nav-link group flex items-center transition-colors duration-200 {{ $active ? 'active' : '' }}" 
           href="{{ route('dashboard') }}">
            
            <i class="fas fa-fw fa-tachometer-alt mr-2 text-sm transition-colors duration-200
                {{ $active ? 'text-[#ffb700]' : 'text-white/60 group-hover:text-white' }}"></i>
            
            <span class="transition-colors duration-200
                {{ $active ? 'text-[#ffb700] font-semibold' : 'text-white/60 group-hover:text-white' }}">
                Dashboard
            </span>
        </a>
    </li>

    @if($isAdmin)
        <hr class="sidebar-divider border-white/10">
        <div class="sidebar-heading text-white/40 text-[10px] uppercase tracking-wider px-3 py-2">
            Menu Principal
        </div>
        @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>



</ul>

</ul>