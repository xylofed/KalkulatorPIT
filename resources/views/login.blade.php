@extends('layouts.app')

@section('content')
<style>
    body, html {
        height: 100%;
        margin: 0;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #e0e7ff;
        overflow-x: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .login-container {
        background: rgba(38, 55, 112, 0.85);
        padding: 40px 35px;
        border-radius: 16px;
        box-shadow: 0 0 25px rgba(37, 117, 252, 0.7);
        width: 100%;
        max-width: 420px;
        color: #e0e7ff;
    }

    h2 {
        color: #ffdd59;
        text-align: center;
        margin-bottom: 30px;
        text-shadow: 0 0 12px rgba(255, 221, 89, 0.7);
    }

    label {
        color: #ffdd59;
        font-weight: 600;
    }

    input.form-control {
        background: rgba(255, 255, 255, 0.15);
        border: 1.5px solid rgba(255, 221, 89, 0.85);
        color: #ffdd59;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 1.1rem;
        transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
    }

    input.form-control::placeholder {
        color: #ffe680;
        opacity: 0.7;
    }

    input.form-control:focus {
        outline: none;
        border-color: #ffdd59;
        background: rgba(255, 255, 255, 0.25);
        color: #222;
    }

    .form-check-label {
        color: #ffdd59;
        font-weight: 600;
    }

    .form-check-input {
        filter: invert(1);
        width: 18px;
        height: 18px;
        margin-top: 3px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #ffdd59;
        border: none;
        color: #222;
        font-weight: 700;
        width: 100%;
        padding: 12px;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(255, 221, 89, 0.6);
        transition: background-color 0.3s ease, color 0.3s ease;
        border-radius: 10px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #f5c700;
        color: #111;
        box-shadow: 0 6px 14px rgba(245, 199, 0, 0.8);
    }

    .text-danger {
        color: #ff6b6b !important;
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>

<div class="login-container">
    <h2>Zaloguj się</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required autofocus placeholder="Wpisz swój email">
            @error('email')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Hasło:</label>
            <input type="password" name="password" class="form-control" id="password" required placeholder="Wpisz hasło">
            @error('password')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Zapamiętaj mnie</label>
        </div>

        <button type="submit" class="btn btn-primary">Zaloguj się</button>
    </form>
</div>
@endsection
