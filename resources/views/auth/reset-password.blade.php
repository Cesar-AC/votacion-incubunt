
@extends('layouts.auth')

@section('title', 'Restablecer contraseña')

@section('content')
<h2 class="text-center text-gray-900 text-lg mb-6">
    Nueva contraseña
</h2>

<x-auth-session-status :status="session('status')" class="mb-4" />

<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="mb-6">
        <label class="block text-gray-600 mb-2 text-sm">Contraseña</label>
        <input type="password" name="password" required
            class="w-full h-[50px] bg-gray-200 rounded-lg px-4 text-sm">
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mb-6">
        <label class="block text-gray-600 mb-2 text-sm">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" required
            class="w-full h-[50px] bg-gray-200 rounded-lg px-4 text-sm">
    </div>

    <button class="w-full h-[50px] bg-[#1e2772] text-white rounded-lg font-semibold">
        Guardar contraseña
    </button>
</form>
@endsection
