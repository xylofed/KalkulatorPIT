<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        // Walidacja danych logowania
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Próba logowania
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            // Jeśli logowanie się powiodło, przekieruj z komunikatem
            return redirect('/')->with('status', 'Zalogowano pomyślnie.');
        }

        // Jeśli niepowodzenie, wróć z błędem
        return back()->withErrors([
            'email' => 'Nieprawidłowe dane logowania.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Wylogowanie użytkownika.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Wylogowano pomyślnie.');
    }
}
