@extends('layouts.auth')

@section('title', 'Recuperar contraseña')

@section('content')
<h2 class="text-center text-gray-900 text-lg mb-4">
    Recuperar contraseña
</h2>

<p class="text-center text-sm text-gray-500 mb-6">
    Te enviaremos un enlace seguro a tu correo.
</p>

<x-auth-session-status :status="session('status')" class="mb-4" />

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-6">
        <label class="block text-gray-600 mb-2 text-sm">Email</label>
        <input type="email" name="email" required
            class="w-full h-[50px] bg-gray-200 rounded-lg px-4 text-sm">
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <button class="w-full h-[50px] bg-[#1e2772] text-white rounded-lg font-semibold">
        Enviar enlace
    </button>
</form>

<div class="text-center mt-4">
    <a href="{{ route('login') }}" class="text-sm underline text-[#1e2772]">
        Volver al login
    </a>
</div>
@endsection
