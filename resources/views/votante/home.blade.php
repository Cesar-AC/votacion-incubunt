@extends('layouts.admin')

@section('content')
<style>
    /* Contenedor principal para centrado absoluto */
    .voter-wrapper {
        min-height: calc(100vh - 160px); /* Ajuste para descontar topbar y footer aproximados */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }

    .voter-card {
        border-radius: 24px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: none;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 30px rgba(124, 58, 237, 0.1);
        position: relative;
    }
    
    .voter-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(124, 58, 237, 0.2);
    }

    /* Card de Votación - Diseño Premium Oscuro */
    .card-vote {
        background: linear-gradient(145deg, #1e1b4b 0%, #4c1d95 100%);
        color: white;
    }
    
    /* Card de Propuestas - Diseño Premium Claro */
    .card-proposals {
        background: #ffffff;
        color: #1e1b4b;
        border: 1px solid rgba(124, 58, 237, 0.1);
    }

    .card-voter-body {
        padding: 60px 40px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .voter-icon-box {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 22px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .card-vote .voter-icon-box {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .card-proposals .voter-icon-box {
        background-color: #f5f3ff;
        border: 1px solid #ddd6fe;
    }

    .voter-icon-box i {
        font-size: 35px;
    }

    .card-vote .voter-icon-box i { color: white; }
    .card-proposals .voter-icon-box i { color: #7c3aed; }

    .voter-card h2 {
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 12px;
        letter-spacing: -1px;
    }

    .brand-text-sm {
        color: #a78bfa;
        font-weight: 700;
        font-size: 1.3rem;
        display: block;
        margin-bottom: 20px;
        text-transform: lowercase;
    }

    .voter-card p {
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 40px;
        max-width: 320px;
        opacity: 0.9;
    }

    .card-vote p { color: #c4b5fd; }
    .card-proposals p { color: #6b7280; }

    /* Botones Optimizados */
    .btn-voter {
        padding: 16px 50px;
        font-weight: 800;
        font-size: 1.1rem;
        border-radius: 15px;
        text-transform: uppercase;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 280px;
        text-decoration: none !important;
        letter-spacing: 1px;
    }

    .btn-voter-light {
        background-color: #ffffff;
        color: #5b21b6;
        border: 2px solid transparent;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .btn-voter-light:hover {
        background-color: #7c3aed;
        color: #ffffff;
        transform: scale(1.05);
        border-color: #7c3aed;
    }

    .btn-voter-outline {
        background-color: transparent;
        color: #7c3aed;
        border: 2px solid #7c3aed;
    }

    .btn-voter-outline:hover {
        background-color: #7c3aed;
        color: white;
        transform: scale(1.05);
    }

    /* Ajustes Responsive */
    @media (max-width: 991px) {
        .voter-wrapper {
            min-height: auto;
            padding: 40px 0;
        }
        .voter-card h2 { font-size: 1.8rem; }
    }
</style>

<div class="container-fluid">
    <div class="voter-wrapper">
        <div class="row w-100 justify-content-center align-items-stretch">
            <!-- Card 1: Votar -->
            <div class="col-xl-5 col-lg-6 col-md-10 mb-4">
                <div class="voter-card card-vote">
                    <div class="card-voter-body">
                        <div class="voter-icon-box">
                            <i class="fas fa-vote-yea"></i>
                        </div>
                        <h2>Elecciones</h2>
                        <span class="brand-text-sm">incubunt 2026</span>
                        <p>Ejerce tu derecho y elige a los líderes que guiarán nuestra organización.</p>
                        
                        @php
                            $primeraEleccion = $eleccionesActivas->first();
                        @endphp

                        @if($primeraEleccion)
                            <a href="{{ route('votante.votar.index', $primeraEleccion->idElecciones) }}" class="btn btn-voter btn-voter-light">
                                Votar Ahora
                            </a>
                        @else
                            <button class="btn btn-voter btn-voter-light opacity-50" style="cursor: not-allowed;" disabled>
                                Sin Elecciones
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Propuestas -->
            <div class="col-xl-5 col-lg-6 col-md-10 mb-4">
                <div class="voter-card card-proposals">
                    <div class="card-voter-body">
                        <div class="voter-icon-box">
                            <i class="fas fa-search"></i>
                        </div>
                        <h2>Propuestas</h2>
                        <span class="brand-text-sm" style="color: #7c3aed;">Conoce a tus candidatos</span>
                        <p>Infórmate sobre los planes de gobierno antes de emitir tu voto informado.</p>
                        
                        <a href="{{ route('votante.elecciones') }}" class="btn btn-voter btn-voter-outline">
                            Ver Propuestas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
