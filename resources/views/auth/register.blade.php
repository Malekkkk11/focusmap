@extends('layouts.app')

@section('content')
<style>
    .register-page {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f7fa, #e0eafc);
    }

    .register-box {
        background-color: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 500px;
    }

    .register-box h2 {
        font-weight: 700;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .register-box p {
        color: #6c757d;
        font-size: 0.95rem;
    }

    .btn-focusmap {
        background-color: #007bff;
        border: none;
        border-radius: 50px;
        padding: 12px;
        font-size: 1rem;
        color: white;
        width: 100%;
        transition: 0.3s ease-in-out;
    }

    .btn-focusmap:hover {
        background-color: #339cff;
    }
</style>

<div class="register-page">
    <div class="register-box">
        <div class="text-center mb-4">
            <h2>Créer un compte 🚀</h2>
            <p>Inscris-toi pour démarrer ta carte mentale avec FocusMap</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nom complet</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       name="name" id="name" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" id="email" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" id="password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-focusmap">S'inscrire</button>

            <p class="text-center text-muted mt-3 mb-0">
                Déjà inscrit ? <a href="{{ route('login') }}" class="text-primary text-decoration-none">Se connecter</a>
            </p>
        </form>
    </div>
</div>
@endsection
