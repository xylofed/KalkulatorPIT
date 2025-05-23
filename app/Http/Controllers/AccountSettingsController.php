<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AccountSettingsController extends Controller
{
    /**
     * Wyświetl formularz edycji danych konta.
     */
    public function edit()
    {
        return view('account.settings');
    }

    /**
     * Aktualizuj dane użytkownika (imię i nazwisko).
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return redirect()->route('account.settings')->with('status', 'Dane użytkownika zaktualizowane.');
    }

    /**
     * Aktualizuj adres e-mail użytkownika.
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->email = $request->email;

        // Jeśli używasz weryfikacji e-mail
        $user->email_verified_at = null;

        $user->save();

        return redirect()->route('account.settings')->with('status', 'E-mail zaktualizowany.');
    }

    /**
     * Zmień hasło użytkownika.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Sprawdzanie, czy aktualne hasło jest poprawne
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Nieprawidłowe aktualne hasło.',
            ]);
        }

        // Zapisz nowe hasło
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('account.settings')->with('status', 'Hasło zmienione.');
    }
}
