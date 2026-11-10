<?php

use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\Products\KitController;
use App\Http\Controllers\Admin\Products\HardwareController;
use App\Http\Controllers\Admin\Products\KitProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\WalletController;
use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySelectionController;
use App\Http\Controllers\CompanyTeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Workspace\AlertController;
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

//Rutas para el carrito de compras
Route::get('/carrito', [CartController::class, 'index'])->name('shop.cart.index');
Route::post('/carrito/agregar/{id}', [CartController::class, 'add'])->name('shop.cart.add');
Route::patch('/carrito/actualizar/{key}', [CartController::class, 'updateQuantity'])->name('shop.cart.update'); // NUEVA
Route::delete('/carrito/eliminar/{key}', [CartController::class, 'removeItem'])->name('shop.cart.remove');


Route::middleware('auth')->group(function () {
    Route::get('/select-company', [CompanySelectionController::class, 'show'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'setCompany'])->name('company.select.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Ruta para la billerta de pagp
    Route::get('/billetera-pagos', [WalletController::class, 'index'])->name('wallet.index');
    // Ruta PARA GUARDAR LA TARJETA:
    Route::post('/billetera-pagos/metodo', [WalletController::class, 'storePaymentMethod'])->name('wallet.payment.store');
    // Dentro del grupo de rutas protegidas por 'auth':
    Route::patch('/billetera-pagos/{id}/predeterminada', [\App\Http\Controllers\Shop\WalletController::class, 'setDefaultPaymentMethod'])->name('wallet.payment.default');

    Route::post('/shop/checkout', [\App\Http\Controllers\Shop\CheckoutController::class, 'process'])->name('shop.checkout.process');

});

// Rutas protegidas que requieren usuario logueado y una empresa activa
Route::middleware(['auth', App\Http\Middleware\SetCurrentCompany::class])->group(function () {

    // Rutas de lectura (Disponibles para todos los miembros)
    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::get('/company/team', [CompanyTeamController::class, 'index'])->name('company.team');
    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::get('/company/activity', [CompanyController::class, 'activity'])->name('company.activity');

    // Historial de Compras del Cliente (E-commerce)
    Route::prefix('mis-compras')->name('client.orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Client\OrderController::class, 'show'])->name('show');
        Route::get('/{id}/pdf', [\App\Http\Controllers\Client\OrderController::class, 'downloadPdf'])->name('pdf');
    });

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

// ==========================================
// Módulo C y D: Operativa B2B (Workspace IoT y Soporte)
// ==========================================
Route::prefix('workspace')->name('workspace.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Workspace\WorkspaceDashboardController::class, 'index'])->name('dashboard');
    Route::get('/devices', [\App\Http\Controllers\Workspace\WorkspaceDashboardController::class, 'devices'])->name('devices');
    Route::get('/tickets', [\App\Http\Controllers\Workspace\TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [\App\Http\Controllers\Workspace\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [\App\Http\Controllers\Workspace\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Workspace\TicketController::class, 'reply'])->name('tickets.reply');
    Route::get('/devices/{id}/metrics', [\App\Http\Controllers\Workspace\WorkspaceDashboardController::class, 'metrics'])->name('devices.metrics');
    Route::post('/devices/redeem', [\App\Http\Controllers\Workspace\WorkspaceDashboardController::class, 'redeem'])->name('devices.redeem');

    // Módulo de Alertas
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts/{id}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');
});

// Grupo exclusivo para el Staff de Sensoría (Super Admins y Soporte)
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {

    // Plataforma
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Pedidos Globales del Staff
    Route::get('/pedidos', [AdminOrderController::class, 'index'])->name('orders.index');

    // Gestión de Empresas
    Route::get('/empresas', [AdminCompanyController::class, 'index'])->name('companies.index');
    Route::post('/empresas', [AdminCompanyController::class, 'store'])->name('companies.store');
    Route::put('/empresas/{company}', [AdminCompanyController::class, 'update'])->name('companies.update');
    Route::patch('/empresas/{company}/toggle-status', [AdminCompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('users.index');

    // Gestión del Equipo de la Empresa (Admin)
    Route::post('/empresas/{company}/equipo', [AdminCompanyTeamController::class, 'store'])->name('companies.team.store');
    Route::put('/empresas/{company}/equipo/{user}', [AdminCompanyTeamController::class, 'update'])->name('companies.team.update');
    Route::delete('/empresas/{company}/equipo/{user}', [AdminCompanyTeamController::class, 'destroy'])->name('companies.team.destroy');

    // Gestión de Usuarios
    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/usuarios', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/usuarios/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/usuarios/{user}/estado', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/usuarios/{user}/password-reset', [AdminUserController::class, 'sendPasswordReset'])->name('users.password-reset');

    // Catálogo
      //Kits
      Route::get('/Products/kits', [KitController::class, 'index'])->name('catalog.kits');
      Route::post('/catalogo/kits', [KitController::class, 'store'])->name('catalog.kits.store');
      Route::put('/catalogo/kits/{id}', [KitController::class, 'update'])->name('catalog.kits.update');
      Route::patch('/catalogo/kits/{id}/toggle-status', [KitController::class, 'toggleStatus'])->name('catalog.kits.toggle');


      //Hardware
      Route::get('/Products/hardware', [HardwareController::class, 'index'])->name('catalog.hardware');
      //Ruta para creacion de hardware
      Route::post('/Products/hardware', [HardwareController::class, 'store'])->name('catalog.hardware.store');
      //Ruta para editar los campos
       Route::put('/Products/hardware/{id}', [HardwareController::class, 'update'])->name('catalog.hardware.update');
       // Ruta rápida para cambiar el estado (Activo/Inactivo)
       Route::patch('/Products/hardware/{id}/toggle-status', [HardwareController::class, 'toggleStatus'])->name('catalog.hardware.toggle');
       Route::get('/Products/hardware/data', [HardwareController::class, 'getData'])->name('catalog.hardware.data');
    Route::get('/Products/hardware/categories', [HardwareController::class, 'getCategories'])->name('catalog.hardware.categories');


    // Gestión de Productos en Kits
    Route::get('/catalogo/kits/productos', [KitProductController::class, 'index'])->name('catalog.kits.products.index');
    Route::get('/catalogo/kits/{kitId}/productos-data', [KitProductController::class, 'getProductsData'])->name('catalog.kits.products.data');
    Route::post('/catalogo/kits/{kitId}/productos/batch', [KitProductController::class, 'batchAddProducts'])->name('catalog.kits.products.batch');
    Route::patch('/catalogo/kits/{kitId}/productos/{productId}', [KitProductController::class, 'updateQuantity'])->name('catalog.kits.products.update');
    Route::delete('/catalogo/kits/{kitId}/productos/{productId}', [KitProductController::class, 'removeProduct'])->name('catalog.kits.products.destroy');

    // Logística y Soporte
    // Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    // Route::get('/tickets', [TicketController::class,y  'index'])->name('tickets.index');
    // Route::get('/bitacora', [AdminLogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/auth.php';
