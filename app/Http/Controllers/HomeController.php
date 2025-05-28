<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaxCalculation;

class HomeController extends Controller
{
    /**
     * Show the public homepage with last tax calculations.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $calculations = TaxCalculation::whereNull('user_id')
            ->where('is_public', true)
            ->latest()
            ->take(5)
            ->get();

        return view('welcome', compact('calculations'));
    }
}
