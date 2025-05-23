@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Historia kalkulacji PIT</h1>

    @if (session('status'))
        <div class="alert alert-success mb-4">{{ session('status') }}</div>
    @endif

    @if ($calculations->isEmpty())
        <div class="alert alert-info">Brak zapisanych kalkulacji.</div>
    @else
        @php
            $actionLabels = [
                'created' => 'Utworzono',
                'updated' => 'Zaktualizowano',
                'deleted' => 'Usunięto',
            ];

            $typeMap = [
                'scale' => 'Skala',
                'flat' => 'Liniowy',
                'ryczałt' => 'Ryczałt',
            ];

            $labels = [
                'income' => 'Przychód',
                'expenses' => 'Koszty uzyskania',
                'deductions' => 'Odliczenia',
                'tax_type' => 'Typ podatku',
                'children' => 'Liczba dzieci',
                'social_insurance' => 'Składki ZUS',
                'health_insurance' => 'Składki zdrowotne',
                'is_married' => 'Wspólne rozliczenie',
                'taxable_income' => 'Dochód do opodatkowania',
                'tax_amount' => 'Kwota podatku',
            ];

            function displayPrev($key, $value) use ($typeMap) {
                if ($key === 'is_married') return $value ? 'Tak' : 'Nie';
                if ($key === 'tax_type') return $typeMap[$value] ?? $value;
                if (is_numeric($value)) return number_format($value, 2, ',', ' ') . ' zł';
                return $value ?? '—';
            }
        @endphp

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Data</th>
                        <th>Dochód</th>
                        <th>Koszty</th>
                        <th>Ulgi</th>
                        <th>Typ podatku</th>
                        <th>Podatek</th>
                        <th>Akcja</th>
                        <th>Użytkownik</th>
                        <th>Poprzednie wartości</th>
                        <th>Opcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calculations as $calc)
                        @foreach ($calc->history as $history)
                            @php
                                $prevValues = is_array($history->previous_values)
                                    ? $history->previous_values
                                    : json_decode($history->previous_values, true);
                            @endphp
                            <tr>
                                <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ number_format($calc->income, 2, ',', ' ') }} zł</td>
                                <td>{{ number_format($calc->expenses, 2, ',', ' ') }} zł</td>
                                <td>{{ number_format($calc->deductions, 2, ',', ' ') }} zł</td>
                                <td>{{ $typeMap[$calc->tax_type] ?? $calc->tax_type }}</td>
                                <td>{{ number_format($calc->tax_amount, 2, ',', ' ') }} zł</td>
                                <td>{{ $actionLabels[$history->action] ?? ucfirst($history->action) }}</td>
                                <td>{{ $history->user->name ?? 'Brak użytkownika' }}</td>
                                <td>
                                    @if (in_array($history->action, ['updated', 'deleted']) && is_array($prevValues) && count($prevValues))
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#detailsCollapse{{ $history->id }}" aria-expanded="false" aria-controls="detailsCollapse{{ $history->id }}">
                                                Pokaż szczegóły
                                            </button>
                                        </div>
                                        <div class="collapse mt-2 text-start" id="detailsCollapse{{ $history->id }}">
                                            <ul class="list-unstyled mt-2">
                                                @foreach ($prevValues as $key => $value)
                                                    <li>
                                                        <strong>{{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        {{ displayPrev($key, $value) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-muted">Brak danych</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('tax-calculations.destroyHistory', $history->id) }}" method="POST" onsubmit="return confirm('Na pewno usunąć?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $calculations->links() }}
        </div>
    @endif
</div>
@endsection
