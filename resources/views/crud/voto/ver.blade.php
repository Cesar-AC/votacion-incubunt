@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    @forelse ($elecciones as $eleccion)
        @include('crud.voto.components.ver-eleccion', compact('eleccion'))
    @empty
        <div class="alert alert-info">
            No hay elecciones programadas.<br/>
            Para ver los resultados de una elección finalizada, hágalo a través de la página de Gestión de Elecciones.
        </div>
    @endforelse
</div>
@endsection
