<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Wyświetla listę użytkowników
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Formularz edycji konkretnego użytkownika
    public function edit($id)
{
    $user = User::findOrFail($id);
    return view('admin.users.edit', compact('user')); // <- poprawna ścieżka
}

    public function destroyAll()
{
    \App\Models\User::where('id', '!=', auth()->id())->delete();

    return redirect()->route('admin.users.index')->with('success', 'Wszyscy użytkownicy oprócz Ciebie zostali usunięci.');
}



    // Aktualizacja danych użytkownika
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Dane użytkownika zostały zaktualizowane.');
    }

    // (Opcjonalnie) Usuwanie użytkownika
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty.');
    }
}
