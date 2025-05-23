<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaxHistory;

class DashboardController extends Controller
{
    public function __construct()
    {

        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }



/*Wyświetla panel administratora.*/
  public function index(){
    $histories = TaxHistory::with('user')->latest()->paginate(10);
      return view('admin.dashboard', compact('histories'));
  }

   public function clearAll(){TaxHistory::truncate();

        return redirect()->back()->with('success', 'Historia zmian została wyczyszczona.');
    }
}

