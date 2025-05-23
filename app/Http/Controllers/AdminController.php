<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaxHistory;

class AdminController extends Controller
{

    public function index()
{
    return view('admin.dashboard'); // ścieżka: resources/views/admin/dashboard.blade.php
}


    public function dashboard()
    {
        $histories = TaxHistory::with('user', 'taxCalculation')->latest()->paginate(20);
        return view('admin.dashboard', compact('histories'));
    }
}
