@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="display-1 text-danger">500</h1>
        <p class="fs-3">Wewnętrzny błąd serwera</p>
        <p class="mb-4">
            Coś poszło nie tak po stronie serwera. Spróbuj ponownie za chwilę lub skontaktuj się z administratorem, jeśli problem będzie się powtarzał.
        </p>
        <a href="{{ url('/') }}" class="btn btn-primary">Powrót do strony głównej</a>
    </div>
@endsection
