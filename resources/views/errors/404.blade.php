@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-1 text-warning">404</h1>
    <p class="fs-4">Strona nie została znaleziona</p>
    <p>Wygląda na to, że adres jest nieprawidłowy lub zasób został usunięty.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Powrót do strony głównej</a>
</div>
@endsection
