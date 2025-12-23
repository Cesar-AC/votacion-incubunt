@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
<h2 class="text-center text-gray-900 text-lg mb-6">
    Iniciar sesión en tu cuenta
</h2>

<form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Email --}}
    <div class="mb-6">
        <label class="block text-gray-600 mb-2 text-sm">Email</label>
        <input type="email" name="email" required
            class="w-full h-[50px] bg-gray-200 rounded-lg px-4 text-sm">
    </div>

    {{-- Password --}}
    <div class="mb-6">
        <label class="block text-gray-600 mb-2 text-sm">Contraseña</label>
        <input type="password" name="password" required
            class="w-full h-[50px] bg-gray-200 rounded-lg px-4 text-sm">
    </div>

    <div class="flex justify-between items-center mb-6 text-sm">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="remember">
            Recordarme
        </label>

        <a href="{{ route('password.request') }}" class="text-[#1e2772] underline">
            ¿Olvidaste tu contraseña?
        </a>
    </div>

    <button class="w-full h-[50px] bg-[#1e2772] text-white rounded-lg font-semibold">
        Iniciar sesión
    </button>
</form>
@endsection
