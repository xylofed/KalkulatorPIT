<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TaxCalculation;
use App\Models\TaxHistory;

class TaxCalculationController extends Controller
{
    private const TAX_REDUCTION = 3600;
    private const CHILD_ALLOWANCE = 1000;

public function showTaxHistory()
{
    $histories = TaxHistory::with([
            'user',
            'taxCalculation' => function($query) {
                $query->withTrashed(); // Jeśli używasz soft delete
            }
        ])
        ->orderByDesc('created_at')
        ->paginate(10);

    return view('admin.dashboard', compact('histories'));
}
    public function destroyHistory($id)
    {
        $history = TaxHistory::findOrFail($id);
        $history->delete();

        return redirect('/admin/dashboard')->with('status', 'Rekord historii został usunięty.');
    }

    public function edit($id)
    {
        $taxCalculation = TaxCalculation::findOrFail($id);
        return view('tax-calculations.edit', compact('taxCalculation'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'income' => 'required|numeric|min:0',
        'expenses' => 'required|numeric|min:0',
        'deductions' => 'required|numeric|min:0',
        'tax_type' => 'required|string|in:scale,flat,ryczałt',
        'children' => 'nullable|numeric|min:0',
        'social_insurance' => 'nullable|numeric|min:0',
        'health_insurance' => 'nullable|numeric|min:0',
        'is_married' => 'nullable|boolean',
    ]);

    $taxCalculation = TaxCalculation::findOrFail($id);

    // Zapisz stare wartości (bez timestampów)
    $oldValues = collect($taxCalculation->getOriginal())
        ->except(['created_at', 'updated_at'])
        ->toArray();

    // Obliczenia
    $taxableIncome = max(0, $request->income - $request->expenses - $request->deductions - ($request->social_insurance ?? 0));
    $taxAmount = $this->calculateTax($taxableIncome, $request->tax_type);
    $taxAmount = max(0, $taxAmount - self::TAX_REDUCTION);

    if ($request->boolean('is_married')) {
        $taxAmount /= 2;
    }

    $taxAmount -= ($request->children ?? 0) * self::CHILD_ALLOWANCE;
    $taxAmount = max(0, $taxAmount);

    // Nowe wartości do zapisania
    $newValues = [
        'income' => $request->income,
        'expenses' => $request->expenses,
        'deductions' => $request->deductions,
        'tax_type' => $request->tax_type,
        'children' => $request->children ?? 0,
        'social_insurance' => $request->social_insurance ?? 0,
        'health_insurance' => $request->health_insurance ?? 0,
        'is_married' => $request->boolean('is_married'),
        'taxable_income' => $taxableIncome,
        'tax_amount' => $taxAmount,
    ];

    // Zapis historii zmian
    TaxHistory::create([
        'tax_calculation_id' => $taxCalculation->id,
        'user_id' => Auth::id(),
        'action' => 'updated',
        'previous_values' => $oldValues,
        'new_values' => $newValues,
    ]);

    // Aktualizacja kalkulacji
    $taxCalculation->update($newValues);

    return redirect()->route('tax-calculations.index')
        ->with('status', 'Kalkulacja zaktualizowana');
}


    public function showCalculator()
    {
        return view('tax-calculator');
    }

    public function index()
    {
        $taxCalculations = TaxCalculation::where('user_id', Auth::id())->get();
        return view('tax-calculations.index', compact('taxCalculations'));
    }

    public function history()
    {
        $calculations = Auth::user()->taxCalculations()->latest()->get();
        return view('tax.history', compact('calculations'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $calculation = $user->taxCalculations()->findOrFail($id);
        $originalValues = collect($calculation->getOriginal())->except(['created_at', 'updated_at'])->toArray();

        TaxHistory::create([
            'tax_calculation_id' => $calculation->id,
            'user_id' => $user->id,
            'action' => 'deleted',
            'previous_values' => $originalValues,
        ]);

        $calculation->delete();

        return redirect()->route('tax-calculations.index')->with('status', 'Kalkulacja została usunięta.');
    }

    public function calculate(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Musisz być zalogowany, aby obliczyć podatek.');
        }

        $validated = $request->validate([
            'income' => 'required|numeric|min:0',
            'expenses' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'tax_type' => 'required|in:scale,flat,ryczałt',
            'children' => 'nullable|numeric|min:0',
            'social_insurance' => 'nullable|numeric|min:0',
            'health_insurance' => 'nullable|numeric|min:0',
            'is_married' => 'nullable|boolean',
        ]);

        $income = $validated['income'];
        $expenses = $validated['expenses'];
        $deductions = $validated['deductions'];
        $taxType = $validated['tax_type'];
        $children = $validated['children'] ?? 0;
        $socialInsurance = $validated['social_insurance'] ?? 0;
        $healthInsurance = $validated['health_insurance'] ?? 0;
        $isMarried = $request->boolean('is_married');

        $taxableIncome = max(0, $income - $expenses - $deductions - $socialInsurance);
        $taxAmount = $this->calculateTax($taxableIncome, $taxType);

        $taxAmount = max(0, $taxAmount - self::TAX_REDUCTION);
        if ($isMarried) {
            $taxAmount /= 2;
        }

        $taxAmount -= $children * self::CHILD_ALLOWANCE;
        $taxAmount = max(0, $taxAmount);

        $taxCalculation = $user->taxCalculations()->create([
            'income' => $income,
            'expenses' => $expenses,
            'deductions' => $deductions,
            'tax_type' => $taxType,
            'children' => $children,
            'social_insurance' => $socialInsurance,
            'health_insurance' => $healthInsurance,
            'is_married' => $isMarried,
            'taxable_income' => $taxableIncome,
            'tax_amount' => $taxAmount,
        ]);

        TaxHistory::create([
    'tax_calculation_id' => $taxCalculation->id,
    'user_id' => $user->id,
    'action' => 'created',
    'previous_values' => null,
    'new_values' => [
        'income' => $taxCalculation->income,
        'expenses' => $taxCalculation->expenses,
        'deductions' => $taxCalculation->deductions,
        'tax_type' => $taxCalculation->tax_type,
        'children' => $taxCalculation->children,
        'social_insurance' => $taxCalculation->social_insurance,
        'health_insurance' => $taxCalculation->health_insurance,
        'is_married' => $taxCalculation->is_married,
        'taxable_income' => $taxCalculation->taxable_income,
        'tax_amount' => $taxCalculation->tax_amount,
    ],
]);


        return redirect()->route('pit-calculator')
            ->with('taxableIncome', $taxableIncome)
            ->with('taxAmount', $taxAmount)
            ->with('status', 'Kalkulacja została pomyślnie zapisana w Twojej bibliotece.');
    }

    private function calculateTax(float $taxableIncome, string $taxType): float
    {
        return match ($taxType) {
            'scale' => $taxableIncome <= 120000
                ? $taxableIncome * 0.17
                : 120000 * 0.17 + ($taxableIncome - 120000) * 0.32,
            'flat' => $taxableIncome * 0.19,
            'ryczałt' => $taxableIncome * 0.085,
            default => throw new \InvalidArgumentException('Nieprawidłowy typ podatku.')
        };
    }

    public function showDemoCalculator()
{
    return view('tax-calculator-demo');
}

public function edit($id)
{
    $history = TaxHistory::findOrFail($id);
    return view('tax-history.edit', compact('history'));
}


public function calculateDemo(Request $request)
{
    $validated = $request->validate([
        'income' => 'required|numeric|min:0',
        'expenses' => 'required|numeric|min:0',
        'deductions' => 'required|numeric|min:0',
        'tax_type' => 'required|in:scale,flat,ryczałt',
        'children' => 'nullable|numeric|min:0',
        'social_insurance' => 'nullable|numeric|min:0',
        'health_insurance' => 'nullable|numeric|min:0',
        'is_married' => 'nullable|boolean',
    ]);

    $income = $validated['income'];
    $expenses = $validated['expenses'];
    $deductions = $validated['deductions'];
    $taxType = $validated['tax_type'];
    $children = $validated['children'] ?? 0;
    $socialInsurance = $validated['social_insurance'] ?? 0;
    $healthInsurance = $validated['health_insurance'] ?? 0;
    $isMarried = $request->boolean('is_married');

    $taxableIncome = max(0, $income - $expenses - $deductions - $socialInsurance);
    $taxAmount = $this->calculateTax($taxableIncome, $taxType);

    $taxAmount = max(0, $taxAmount - self::TAX_REDUCTION);
    if ($isMarried) {
        $taxAmount /= 2;
    }

    $taxAmount -= $children * self::CHILD_ALLOWANCE;
    $taxAmount = max(0, $taxAmount);

    return view('tax-calculator-demo', [
        'taxableIncome' => $taxableIncome,
        'taxAmount' => $taxAmount,
        'input' => $validated,
    ]);
}

}
