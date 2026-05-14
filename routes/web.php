<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\CatalogTypeController;
use App\Http\Controllers\CatalogCategoryController;
use App\Http\Controllers\CatalogItemController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rutas publicas
Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::redirect('/catalogo', '/');
Route::get('/catalogo/producto/{product}', [CatalogoController::class, 'showProduct'])->name('catalogo.product');
Route::get('/catalogo/servicio/{service}', [CatalogoController::class, 'showService'])->name('catalogo.service');
Route::get('/catalogo/buscar', [CatalogoController::class, 'buscar'])->name('catalogo.buscar');

// Carrito publico
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::delete('/carrito/quitar/{id}', [CarritoController::class, 'quitar'])->name('carrito.quitar');
Route::delete('/carrito/limpiar', [CarritoController::class, 'limpiar'])->name('carrito.limpiar');

// Checkout y pagos publicos (invitado o autenticado)
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/orden/crear', [OrderController::class, 'store'])->name('orden.store');
Route::post('/reservas/servicio/{service}', [OrderController::class, 'reservarServicio'])->name('reservas.servicio');
Route::post('/reservas/catalogo', [OrderController::class, 'reservarCatalogo'])->name('reservas.catalogo');
Route::get('/orden/{order}/confirmacion', [OrderController::class, 'confirmacion'])->name('orden.confirmacion');
Route::get('/payphone/success', [TransactionController::class, 'success'])->name('payphone.success');
Route::get('/payphone/cancel', [TransactionController::class, 'cancel'])->name('payphone.cancel');

// Redireccion post-login segun rol
Route::get('/dashboard', function () {
    if (Auth::user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware('auth')->name('dashboard');

// Rutas autenticadas
Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Detalle de orden para usuarios autenticados
    Route::get('/orden/{order}', [OrderController::class, 'show'])->name('orden.show');
});

// Panel cliente
Route::middleware(['auth', 'role:cliente'])
    ->prefix('/customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/compras', [ClienteController::class, 'compras'])->name('compras');
    });

// Panel admin
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
        Route::view('/catalogo', 'admin.catalog.index')->name('catalog.index');
        Route::resource('/catalogo/tipos', CatalogTypeController::class)
            ->parameters(['tipos' => 'catalogType'])
            ->names('catalog-types');
        Route::resource('/catalogo/categorias', CatalogCategoryController::class)
            ->parameters(['categorias' => 'catalogCategory'])
            ->names('catalog-categories');
        Route::resource('/catalogo/items', CatalogItemController::class)
            ->parameters(['items' => 'catalogItem'])
            ->names('catalog-items');

        // Empresa
        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
        Route::delete('/empresa/logo', [EmpresaController::class, 'deleteLogo'])->name('empresa.deleteLogo');

        // Landing Banners
        Route::resource('/banners', BannerController::class);

        // CRUD
        Route::resource('/categories', CategoryController::class);
        Route::resource('/products', ProductController::class);
        Route::resource('/services', ServiceController::class);
        Route::resource('/orders', OrderController::class)->only(['index', 'show', 'destroy']);
        Route::patch('/orders/{order}/marcar-pagada', [OrderController::class, 'marcarPagada'])->name('orders.marcar-pagada');
        Route::resource('/transactions', TransactionController::class)->only(['index', 'show']);
    });

require __DIR__ . '/auth.php';
