<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyTeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\SetCurrentCompany;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas protegidas que requieren usuario logueado y una empresa activa en sesión
Route::middleware(['auth', SetCurrentCompany::class])->group(function () {

    // Gestión de la Empresa/Tenant (Módulo A)
    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('/company', [CompanyController::class, 'update'])->name('company.update');
    Route::get('/company/team', [CompanyTeamController::class, 'index'])->name('company.team');
});

require __DIR__.'/auth.php';
