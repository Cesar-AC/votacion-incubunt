<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'VOTAINCUBI')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('img/VOTAINCUBI_mobile.svg') }}" type="image/svg+xml">

    <!-- Font Awesome (opcional) -->
    <link rel="stylesheet" href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}">
</head>

<body>

<!-- BACKGROUND -->
<div class="relative min-h-screen w-full overflow-hidden bg-[#020617]">

    <!-- Glow + Grid -->
    <div
        class="absolute inset-0 z-0"
        style="
            background-image:
              linear-gradient(to right, rgba(71,85,105,0.25) 1px, transparent 1px),
              linear-gradient(to bottom, rgba(71,85,105,0.25) 1px, transparent 1px),
              radial-gradient(circle 600px at 50% 120px, rgba(139,92,246,0.35), transparent 70%);
            background-size: 32px 32px, 32px 32px, 100% 100%;
        ">
    </div>

    <!-- CENTER WRAPPER -->
    <div class="relative z-10 flex min-h-screen items-center justify-center px-4 sm:px-6">

        <!-- CARD -->
<div
  class="mx-auto flex w-full max-w-md lg:max-w-4xl
         overflow-hidden rounded-3xl
         bg-gradient-to-br from-white via-slate-50 to-violet-50
         backdrop-blur-xl
         border border-white/40
         shadow-[0_25px_80px_-20px_rgba(139,92,246,0.45)]">


            <!-- LEFT PANEL (desktop only) -->
            <div class="hidden lg:flex lg:w-1/2 items-center justify-center bg-slate-100">
                <img
                    src="{{ asset('img/VOTAINCUBI_placeholder.png') }}"
                    alt="Ilustración"
                    class="max-h-[420px] object-contain opacity-90"
                />
            </div>

            <!-- LOGIN PANEL -->
            <div class="w-full lg:w-1/2 px-6 py-8 sm:px-10 sm:py-10">

                <!-- LOGO -->
                <div class="mb-6 text-center">
                    <img
                        src="{{ asset('img/VOTAINCUBI.png') }}"
                        alt="Logo VOTAINCUBI"
                        class="mx-auto mb-4 h-12 sm:h-14"
                    />
                    <h1 class="text-lg sm:text-xl font-semibold text-gray-900">
                        Iniciar sesión
                    </h1>
                </div>

                <!-- ERRORS -->
               @if ($errors->any())
  <div
    class="mb-5 rounded-xl border border-red-200
           bg-red-50 px-4 py-3 text-sm text-red-700
           shadow-sm">
    <ul class="space-y-1">
      @foreach ($errors->all() as $error)
        <li class="flex items-center gap-2">
          <span class="text-red-500">●</span>
          {{ $error }}
        </li>
      @endforeach
    </ul>
  </div>
@endif


                <!-- FORM -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- EMAIL -->
                    <div>
                        <label for="email" class="mb-1 block text-sm text-gray-600">
                            Email
                        </label>
                       <input
    id="email"
    type="email"
    name="email"
    required
    autofocus
    placeholder="Ingresa tu correo"
    class="h-11 sm:h-12 w-full rounded-lg
           bg-gray-100 px-4 text-sm
           placeholder-gray-400
           focus:outline-none focus:ring-2 focus:ring-violet-600"
/>

                    </div>

                    <!-- PASSWORD -->
                    <div>
                        <label for="password" class="mb-1 block text-sm text-gray-600">
                            Contraseña
                        </label>
                       <input
    id="password"
    type="password"
    name="password"
    required
    placeholder="Ingresa tu contraseña"
    class="h-11 sm:h-12 w-full rounded-lg
           bg-gray-100 px-4 text-sm
           placeholder-gray-400
           focus:outline-none focus:ring-2 focus:ring-violet-600"
/>

                    </div>

                    <!-- OPTIONS -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-gray-600">
                            <input type="checkbox" name="remember" class="rounded border-gray-300">
                            Recordarme
                        </label>

                    </div>

                    <!-- BUTTON -->
                    <button
                        type="submit"
                        class="mt-4 h-11 sm:h-12 w-full rounded-lg
                               bg-violet-600 text-white font-semibold
                               shadow-lg transition hover:bg-violet-500 class bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500">
                        Iniciar sesión
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
