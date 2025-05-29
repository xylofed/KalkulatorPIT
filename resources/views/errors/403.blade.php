@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="display-1 text-warning">403</h1>
        <p class="fs-3">Brak dostępu</p>
        <p class="mb-4">Nie masz uprawnień, aby wyświetlić tę stronę.</p>
        @auth
            <a href="{{ url('/home') }}" class="btn btn-primary">Przejdź do strony głównej</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Zaloguj się</a>
        @endauth
    </div>
@endsection
