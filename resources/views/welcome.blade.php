@extends('layouts.app')

@section('content')
<style>
    .hero-section {
        min-height: 92vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: linear-gradient(135deg, #fdfbfb, #ebedee);
        padding: 60px 20px;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        color: #6c757d;
        max-width: 700px;
        margin: 0 auto 30px;
    }

    .btn-primary-custom {
        background-color: #0077ff;
        color: white;
        border: none;
        padding: 12px 32px;
        font-size: 1.1rem;
        border-radius: 50px;
        transition: all 0.3s ease-in-out;
    }

    .btn-primary-custom:hover {
        background-color: #339cff;
        transform: translateY(-2px);
    }

    .btn-outline-custom {
        background-color: transparent;
        color: #0077ff;
        border: 2px solid #0077ff;
        padding: 12px 32px;
        font-size: 1.1rem;
        border-radius: 50px;
        transition: all 0.3s ease-in-out;
    }

    .btn-outline-custom:hover {
        background-color: #0077ff;
        color: white;
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 2.2rem;
        }
        .hero-subtitle {
            font-size: 1rem;
        }
    }
</style>

<div class="container hero-section">
    <div class="col-lg-10 mx-auto">
        <h1 class="hero-title">FocusMap — Visualise tes objectifs</h1>
        <p class="hero-subtitle">
            Trace ta route vers le succès avec une carte mentale interactive. Décompose tes rêves en étapes concrètes, localise-les, et motive-toi jour après jour.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary-custom">Se connecter</a>
                <a href="{{ route('register') }}" class="btn btn-outline-custom">Créer un compte</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary-custom">Accéder à mon dashboard</a>
            @endguest
        </div>
    </div>
</div>
@endsection
