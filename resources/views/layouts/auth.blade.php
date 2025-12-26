<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'VOTAINCUBI')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                </div>

                {{-- Allow child views to inject their own auth forms/content --}}
                @yield('content')
            </div>
        </div>
    </div>
</div>

</body>
</html>
