@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa, #e0eafc);
    }

    .login-container {
        min-height: 92vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background-color: #fff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 480px;
    }

    .form-title {
        font-weight: 700;
        font-size: 1.8rem;
        color: #2c3e50;
    }

    .btn-focusmap {
        background-color: #007bff;
        border: none;
        border-radius: 50px;
        padding: 12px;
        font-size: 1rem;
        color: white;
        width: 100%;
        transition: background-color 0.3s ease-in-out;
    }

    .btn-focusmap:hover {
        background-color: #339cff;
    }

    .form-check-label {
        color: #6c757d;
    }

    .form-floating label {
        padding: 0.75rem 1rem;
    }
</style>

<div class="container login-container">
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="form-title">Bienvenue sur FocusMap 👋</h2>
            <p class="text-muted">Connecte-toi pour accéder à ta carte mentale personnelle</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
            @csrf

            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                    id="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                <label for="email">Adresse e-mail</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    id="password" placeholder="Mot de passe" required>
                <label for="password">Mot de passe</label>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="text-decoration-none text-primary" href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn btn-focusmap mb-3">Connexion</button>

            <p class="text-center text-muted mt-3 mb-0">
                Pas encore inscrit ?
                <a href="{{ route('register') }}" class="text-primary text-decoration-none">Créer un compte</a>
            </p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
@endpush
