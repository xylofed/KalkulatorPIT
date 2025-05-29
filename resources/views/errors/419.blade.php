@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="display-1 text-warning">419</h1>
        <p class="fs-3">Sesja wygasła</p>
        <p class="mb-4">
            Twoja sesja wygasła z powodu braku aktywności lub błędnego tokenu CSRF.
            Odśwież stronę lub zaloguj się ponownie, aby kontynuować.
        </p>
        <a href="{{ route('login') }}" class="btn btn-primary">Zaloguj się ponownie</a>
    </div>
@endsection
