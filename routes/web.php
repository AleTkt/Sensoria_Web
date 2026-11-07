<?php

use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySelectionController;
use App\Http\Controllers\CompanyTeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\SetCurrentCompany;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\ProductCatalogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Public Invitation Routes
Route::get('/invitacion/{token}', [InvitationController::class, 'showAcceptForm'])->name('invitations.accept');
Route::post('/invitacion/{token}', [InvitationController::class, 'accept'])->name('invitations.process');

//Rutas del E-Commerce B2B
Route::get('shop/products', [ProductCatalogController::class, 'index']) ->name('shop.products.index');
Route::get('/tienda/{id}', [ProductCatalogController::class, 'show'])->name('shop.products.show');


Route::middleware('auth')->group(function () {
    Route::get('/select-company', [CompanySelectionController::class, 'show'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'setCompany'])->name('company.select.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



});

// Rutas protegidas que requieren usuario logueado y una empresa activa
Route::middleware(['auth', App\Http\Middleware\SetCurrentCompany::class])->group(function () {

    // Rutas de lectura (Disponibles para todos los miembros)
    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::get('/company/team', [CompanyTeamController::class, 'index'])->name('company.team');
    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::get('/company/activity', [CompanyController::class, 'activity'])->name('company.activity');

    // Módulo A: Edición de Empresa (Solo Propietarios y Administradores)
    Route::middleware('role:PROPIETARIO,ADMINISTRADOR')->group(function () {
        Route::patch('/company', [CompanyController::class, 'update'])->name('company.update');
        Route::put('/company/settings', [CompanyController::class, 'updateSettings'])->name('company.settings.update');

        // Gestión de Equipo
        Route::post('/company/team', [CompanyTeamController::class, 'store'])->name('company.team.store');
        Route::put('/company/team/{id}', [CompanyTeamController::class, 'update'])->name('company.team.update');
        Route::delete('/company/team/{id}', [CompanyTeamController::class, 'destroy'])->name('company.team.destroy');
    });

    // Zona de Peligro (Únicamente Propietarios)
    Route::middleware('role:PROPIETARIO')->group(function () {
        Route::post('/company/transfer-ownership', [CompanyController::class, 'transferOwnership'])->name('company.transfer');
        Route::delete('/company/destroy', [CompanyController::class, 'destroy'])->name('company.destroy');
    });
});

// Grupo exclusivo para el Staff de Sensoría (Super Admins y Soporte)
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {

    // Plataforma
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/empresas', [AdminCompanyController::class, 'index'])->name('companies.index');
    Route::patch('/empresas/{company}/toggle-status', [AdminCompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('users.index');

    // Catálogo
    // Route::get('/catalogo/kits', [KitController::class, 'index'])->name('catalog.kits');
    // Route::get('/catalogo/hardware', [HardwareController::class, 'index'])->name('catalog.hardware');
    // Route::get('/catalogo/categorias', [CategoryController::class, 'index'])->name('catalog.categories');

    // Logística y Soporte
    // Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    // Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    // Route::get('/bitacora', [AdminLogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/auth.php';
