<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Página principal — catálogo público
Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');
Route::get('/catalogo/producto/{product}', [CatalogoController::class, 'showProduct'])->name('catalogo.product');
Route::get('/catalogo/servicio/{service}', [CatalogoController::class, 'showService'])->name('catalogo.service');

// Carrito — público pero requiere auth para pagar
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::delete('/carrito/quitar/{id}', [CarritoController::class, 'quitar'])->name('carrito.quitar');
Route::delete('/carrito/limpiar', [CarritoController::class, 'limpiar'])->name('carrito.limpiar');

// Perfil usuario autenticado
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Checkout y órdenes
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orden/crear', [OrderController::class, 'store'])->name('orden.store');
    Route::get('/orden/{order}', [OrderController::class, 'show'])->name('orden.show');
    Route::get('/orden/{order}/confirmacion', [OrderController::class, 'confirmacion'])->name('orden.confirmacion');

    // Payphone callbacks
    Route::get('/payphone/success', [TransactionController::class, 'success'])->name('payphone.success');
    Route::get('/payphone/cancel', [TransactionController::class, 'cancel'])->name('payphone.cancel');
});

// Admin — solo rol admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'show']);
});

require __DIR__ . '/auth.php';