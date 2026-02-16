<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>VotaIncubi - Sistema de Votación</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Estilos personalizados del stack -->
    @stack('styles')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('components.sidebar_mobile')
        
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column ">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 inline text-gray-600 small">{{ optional(Auth::user()->perfil)->obtenerNombreApellidoCorto() ?? Auth::user()->correo ?? 'Usuario' }}</span>
                                <img class="img-profile rounded-circle" src="{{ Auth::user()->perfil?->obtenerFotoURL() ?? asset('img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <!-- Temporalmente deshabilitado
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configuración
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Registro de Actividad
                                </a>
                                -->
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="bg-white d-block py-8 mb-0">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; VotaIncubi {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top" style="bottom: 5rem;">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Selecciona "Cerrar Sesión" si estás listo para finalizar tu sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sbadmin/js/sb-admin-2.min.js') }}"></script>

    <!-- Script para el sidebar mobile -->
    <script>
        $(document).ready(function() {
            // Cerrar modal al hacer click en cualquier enlace dentro del modal (excepto cerrar sesión)
            $('#mobileMenuModal .list-group-item-action').on('click', function() {
                // Si no es el botón de cerrar sesión, cerrar el modal
                if (!$(this).hasClass('text-danger')) {
                    $('#mobileMenuModal').modal('hide');
                }
            });
            
            // Agregar feedback visual al hacer tap en bottom nav
            $('.navbar.fixed-bottom a').on('touchstart', function() {
                $(this).css('opacity', '0.7');
            }).on('touchend touchcancel', function() {
                $(this).css('opacity', '1');
            });
            
            // Prevenir scroll cuando el modal está abierto en mobile
            $('#mobileMenuModal').on('show.bs.modal', function() {
                $('body').css('overflow', 'hidden');
            }).on('hidden.bs.modal', function() {
                $('body').css('overflow', 'auto');
            });
            
            // Mejorar accesibilidad: cerrar modal con tecla Escape
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#mobileMenuModal').hasClass('show')) {
                    $('#mobileMenuModal').modal('hide');
                }
            });
            
            // Destacar item activo en el bottom nav
            var currentPath = window.location.pathname;
            $('.navbar.fixed-bottom a').each(function() {
                var href = $(this).attr('href');
                if (href && currentPath.includes(href) && href !== '#') {
                    $(this).find('i, span').css('color', '#161349');
                }
            });
        });
    </script>

    @stack('scripts')
    
</body>

</html>