<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\TaxCalculationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaxHistoryController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Autoryzacja
Auth::routes();

// Trasy dostępne tylko dla zalogowanych użytkowników
Route::middleware(['auth'])->group(function () {
    // Kalkulator PIT
    Route::get('/pit-calculator', [TaxCalculationController::class, 'showCalculator'])->name('pit-calculator');
    Route::post('/pit-calculator', [TaxCalculationController::class, 'calculate'])->name('pit-calculator.calculate');

    // Historia kalkulacji PIT
    Route::get('/tax/history', [TaxCalculationController::class, 'history'])->name('tax.history');
    Route::delete('/tax/history/{id}', [TaxCalculationController::class, 'destroyHistory'])->name('tax-history.destroy');

    // Trasy do kalkulacji PIT (CRUD)
    Route::prefix('tax-calculations')->name('tax-calculations.')->group(function () {
        Route::get('/', [TaxCalculationController::class, 'index'])->name('index');
        Route::post('/', [TaxCalculationController::class, 'store'])->name('store');
        Route::get('{id}/edit', [TaxCalculationController::class, 'edit'])->name('edit');
        Route::put('{id}', [TaxCalculationController::class, 'update'])->name('update');
        Route::delete('{id}', [TaxCalculationController::class, 'destroy'])->name('destroy');
    });

    // Ustawienia konta
    Route::get('/account/settings', [AccountSettingsController::class, 'edit'])->name('account.settings');
    Route::put('/account/update-profile', [AccountSettingsController::class, 'updateProfile'])->name('account.updateProfile');
    Route::put('/account/update-email', [AccountSettingsController::class, 'updateEmail'])->name('account.updateEmail');
    Route::put('/account/update-password', [AccountSettingsController::class, 'updatePassword'])->name('account.updatePassword');


    // Trasy dostępne tylko dla administratorów
    Route::middleware([AdminMiddleware::class])->group(function () {

        Route::delete('/admin/dashboard/clear-history', [TaxCalculationController::class, 'destroyAll'])->name('dashboard.clear-history');

        Route::delete('/admin/users/destroy-all', [UserController::class, 'destroyAll'])->name('admin.users.destroyAll');

        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        Route::resource('tax-history', TaxHistoryController::class)->only(['index', 'edit', 'update']);


        Route::resource('users', UserController::class)->only(['edit', 'update']);

        // Dashboard admina
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Panel admina (np. wykresy)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    });
});

// Trasy użytkownika
Route::middleware(['auth'])->prefix('tax-calculations')->name('tax-calculations.')->group(function () {
    Route::get('/', [TaxCalculationController::class, 'index'])->name('index');
    Route::post('/', [TaxCalculationController::class, 'store'])->name('store');
    Route::get('{id}/edit', [TaxCalculationController::class, 'edit'])->name('edit');
    Route::put('{id}', [TaxCalculationController::class, 'update'])->name('update');
    Route::delete('{id}', [TaxCalculationController::class, 'destroy'])->name('destroy');
});

// Logowanie i wylogowanie
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Strona domowa
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Demo kalkulatora PIT dla gości (bez zapisu do bazy)
Route::get('/pit-calculator/demo', [TaxCalculationController::class, 'showDemoCalculator'])->name('pit-calculator.demo');
Route::post('/pit-calculator/demo', [TaxCalculationController::class, 'calculateDemo'])->name('pit-calculator.demo.calculate');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {});
