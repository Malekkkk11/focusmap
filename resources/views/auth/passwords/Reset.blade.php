@extends('layouts.app')

@section('content')
<style>
    .reset-page {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f7fa, #e0eafc);
    }

    .reset-box {
        background-color: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 500px;
    }

    .reset-box h2 {
        font-weight: 700;
        margin-bottom: 10px;
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
        transition: 0.3s ease-in-out;
    }

    .btn-focusmap:hover {
        background-color: #339cff;
    }
</style>

<div class="reset-page">
    <div class="reset-box">
        <div class="text-center mb-4">
            <h2>🔐 Réinitialise ton mot de passe</h2>
            <p>Entre ton nouveau mot de passe ci-dessous</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') ?? request()->email }}" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirme le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-focusmap">Réinitialiser</button>

            <p class="text-center text-muted mt-3 mb-0">
                <a href="{{ route('login') }}" class="text-decoration-none text-primary">Retour à la connexion</a>
            </p>
        </form>
    </div>
</div>
@endsection
