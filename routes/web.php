<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientesController;
use App\Http\Controllers\Admin\VentasController;
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Admin\VehiculosController;
use App\Http\Controllers\Admin\VehicleSpecificationsController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\CatalogTypeController;
use App\Http\Controllers\CatalogCategoryController;
use App\Http\Controllers\CatalogItemController;
use App\Http\Controllers\CatalogItemVariantController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rutas publicas
Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::redirect('/catalogo', '/');
Route::get('/catalogo/buscar', [CatalogoController::class, 'buscar'])->name('catalogo.buscar');

// Carrito publico
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::delete('/carrito/quitar/{id}', [CarritoController::class, 'quitar'])->name('carrito.quitar');
Route::delete('/carrito/limpiar', [CarritoController::class, 'limpiar'])->name('carrito.limpiar');

// Checkout y pagos publicos (invitado o autenticado)
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/orden/crear', [OrderController::class, 'store'])->name('orden.store');
Route::post('/orden/cajita', [OrderController::class, 'prepareBox'])->name('orden.cajita');
Route::post('/reservas/catalogo', [OrderController::class, 'reservarCatalogo'])->name('reservas.catalogo');
Route::get('/orden/{order}/confirmacion', [OrderController::class, 'confirmacion'])->name('orden.confirmacion');
Route::get('/transaccion-exitosa', [TransactionController::class, 'success'])->name('transaccion.exitosa');
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
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/ventas', VentasController::class)
            ->parameters(['ventas' => 'venta'])
            ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('/clientes/quick-store', [ClientesController::class, 'quickStore'])->name('clientes.quick-store');
        Route::resource('/clientes', ClientesController::class)
            ->parameters(['clientes' => 'cliente'])
            ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/vehiculos/especificaciones', [VehicleSpecificationsController::class, 'index'])
            ->name('vehiculos.specifications.index');
        Route::post('/vehiculos/especificaciones/tipos', [VehicleSpecificationsController::class, 'storeType'])
            ->name('vehiculos.specifications.types.store');
        Route::put('/vehiculos/especificaciones/tipos/{vehicleType}', [VehicleSpecificationsController::class, 'updateType'])
            ->name('vehiculos.specifications.types.update');
        Route::delete('/vehiculos/especificaciones/tipos/{vehicleType}', [VehicleSpecificationsController::class, 'destroyType'])
            ->name('vehiculos.specifications.types.destroy');
        Route::post('/vehiculos/especificaciones/marcas', [VehicleSpecificationsController::class, 'storeBrand'])
            ->name('vehiculos.specifications.brands.store');
        Route::put('/vehiculos/especificaciones/marcas/{vehicleBrand}', [VehicleSpecificationsController::class, 'updateBrand'])
            ->name('vehiculos.specifications.brands.update');
        Route::delete('/vehiculos/especificaciones/marcas/{vehicleBrand}', [VehicleSpecificationsController::class, 'destroyBrand'])
            ->name('vehiculos.specifications.brands.destroy');
        Route::post('/vehiculos/especificaciones/modelos', [VehicleSpecificationsController::class, 'storeModel'])
            ->name('vehiculos.specifications.models.store');
        Route::put('/vehiculos/especificaciones/modelos/{vehicleModel}', [VehicleSpecificationsController::class, 'updateModel'])
            ->name('vehiculos.specifications.models.update');
        Route::delete('/vehiculos/especificaciones/modelos/{vehicleModel}', [VehicleSpecificationsController::class, 'destroyModel'])
            ->name('vehiculos.specifications.models.destroy');
        Route::resource('/vehiculos', VehiculosController::class)
            ->parameters(['vehiculos' => 'vehiculo'])
            ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('/usuarios', UsuariosController::class)
            ->parameters(['usuarios' => 'usuario'])
            ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/create', [InventarioController::class, 'create'])->name('inventario.create');
        Route::post('/inventario/movimientos', [InventarioController::class, 'storeMovement'])->name('inventario.movements.store');
        Route::get('/inventario/movimientos/{movement}/edit', [InventarioController::class, 'edit'])->name('inventario.movements.edit');
        Route::put('/inventario/movimientos/{movement}', [InventarioController::class, 'update'])->name('inventario.movements.update');
        Route::delete('/inventario/movimientos/{movement}', [InventarioController::class, 'destroy'])->name('inventario.movements.destroy');
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
        Route::resource('/catalogo/variantes', CatalogItemVariantController::class)
            ->parameters(['variantes' => 'catalogVariant'])
            ->names('catalog-variants');

        // Empresa
        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
        Route::delete('/empresa/logo', [EmpresaController::class, 'deleteLogo'])->name('empresa.deleteLogo');

        // Landing Banners
        Route::resource('/banners', BannerController::class);

        Route::resource('/orders', OrderController::class)->only(['index', 'show', 'destroy']);
        Route::patch('/orders/{order}/marcar-pagada', [OrderController::class, 'marcarPagada'])->name('orders.marcar-pagada');
        Route::resource('/transactions', TransactionController::class)->only(['index', 'show']);
    });

require __DIR__ . '/auth.php';
