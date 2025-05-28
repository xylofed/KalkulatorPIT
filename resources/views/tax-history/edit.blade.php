@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Edytuj rekord historii podatkowej</h1>

    <form action="{{ route('tax-history.update', $history->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="income" class="form-label">Przychód</label>
            <input type="number" step="0.01" class="form-control @error('income') is-invalid @enderror" id="income" name="income" value="{{ old('income', $data['income'] ?? '') }}" required>
            @error('income')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>



        <div class="mb-3">
            <label for="tax_type" class="form-label">Typ podatku</label>
            <select class="form-select @error('tax_type') is-invalid @enderror" id="tax_type" name="tax_type" required>
                <option value="scale" {{ (old('tax_type', $data['tax_type'] ?? '') == 'scale') ? 'selected' : '' }}>Skala</option>
                <option value="flat" {{ (old('tax_type', $data['tax_type'] ?? '') == 'flat') ? 'selected' : '' }}>Liniowy</option>
                <option value="ryczałt" {{ (old('tax_type', $data['tax_type'] ?? '') == 'ryczałt') ? 'selected' : '' }}>Ryczałt</option>
            </select>
            @error('tax_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>



        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
        <a href="{{ route('tax-history.index') }}" class="btn btn-secondary ms-2">Anuluj</a>
    </form>
</div>
@endsection
