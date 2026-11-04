<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySelectionController;
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
    Route::get('/select-company', [CompanySelectionController::class, 'show'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'setCompany'])->name('company.select.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas protegidas que requieren usuario logueado y una empresa activa en sesión
Route::middleware(['auth', SetCurrentCompany::class])->group(function () {

    // Gestión de la Empresa/Tenant (Módulo A)
    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('/company', [CompanyController::class, 'update'])->name('company.update');

    // Gestión de Equipo
    Route::get('/company/team', [CompanyTeamController::class, 'index'])->name('company.team');
    Route::post('/company/team', [CompanyTeamController::class, 'store'])->name('company.team.store');
    Route::put('/company/team/{id}', [CompanyTeamController::class, 'update'])->name('company.team.update');
    Route::delete('/company/team/{id}', [CompanyTeamController::class, 'destroy'])->name('company.team.destroy');
});

require __DIR__.'/auth.php';
