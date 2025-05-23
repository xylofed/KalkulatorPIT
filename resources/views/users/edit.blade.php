@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Edycja użytkownika</h2>

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Imię i nazwisko</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Rola</label>
            <select name="role" id="role" class="form-select" required>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Użytkownik</option>
            </select>
        </div>
        <div class="mb-3">
    <label for="password" class="form-label">Nowe hasło (opcjonalnie)</label>
    <input type="password" name="password" id="password" class="form-control">
</div>

<div class="mb-3">
    <label for="password_confirmation" class="form-label">Potwierdź hasło</label>
    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
</div>

        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Anuluj</a>
    </form>
</div>
@endsection
