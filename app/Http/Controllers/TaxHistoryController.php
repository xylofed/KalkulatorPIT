<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TaxHistory;




class TaxHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $histories = TaxHistory::latest()->paginate(10);
    return view('tax-history.index', compact('histories'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $history = TaxHistory::findOrFail($id);

    // Załaduj powiązaną kalkulację
    $taxCalculation = $history->taxCalculation;

    return view('tax-calculations.edit', compact('taxCalculation'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $history = TaxHistory::findOrFail($id);

    // Przykład: aktualizacja tylko komentarza lub notatki
    $request->validate([
        'note' => 'nullable|string|max:1000',
    ]);

    $history->note = $request->note;
    $history->save();

    return redirect()->route('admin.dashboard')->with('status', 'Wpis historii został zaktualizowany.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
