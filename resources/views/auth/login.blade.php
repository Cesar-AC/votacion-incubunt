<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Login screen for Votaincubi Voting System">
    <meta name="author" content="">

    <title>VOTAINCUBI - Iniciar Sesión</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    </head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5 login-card">
                    <div class="card-body p-0">
                        <div class="row">
                            
                           <div class="col-lg-6 d-none d-lg-block login-illustration-panel">
    <img src="{{ asset('img/VOTAINCUBI_placeholder.png') }}" alt="Ilustración de seguridad">
</div>
                            
                            <div class="col-lg-6 login-panel">
                                <div class="p-5">
                                    <div class="logo-container">
                                        <img src="{{ asset('img/VOTAINCUBI.png') }}" alt="VOTAINCUBI Logo">
                                    </div>
                                    <div class="text-center">
                                        <h2 class="h5 text-gray-900 mb-4">Iniciar sesión en tu cuenta</h2>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <strong>Error!</strong> Por favor verifica los datos ingresados.
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <div class="form-group">
                                            <label for="email">Email :</label>
                                            <div class="input-icon-group">
                                                <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror"
                                                    id="email" name="email" value="{{ old('email', 'usuario@gmail.com') }}"
                                                    placeholder="Email Address" required autofocus>
                                                <div class="icon-box"><i class="fas fa-envelope"></i></div>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Contraseña :</label>
                                            <div class="input-icon-group">
                                                <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Ingresa tu contraseña" required>
                                                <div class="icon-box"><i class="fas fa-lock"></i></div>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-6">
                                                <div class="custom-control custom-checkbox small">
                                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                                    <label class="custom-control-label" for="remember">¿Recordar contraseña?</label>
                                                </div>
                                            </div>
                                            <div class="col-6 text-right">
                                                <a class="forgot-password-link" href="#">¿Olvidaste tu contraseña?</a>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group mt-5">
                                            <button type="submit" class="btn btn-primary btn-user btn-block btn-login">
                                                Iniciar sesión
                                            </button>
                                        </div>
                                    </form>

                                    <hr style="visibility: hidden;">
                                    <div class="text-center" style="visibility: hidden;">
                                        <a class="small" href="#">Forgot Password?</a>
                                    </div>
                                    <div class="text-center" style="visibility: hidden;">
                                        <a class="small" href="#">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin/js/sb-admin-2.min.js') }}"></script>

</body>

</html>