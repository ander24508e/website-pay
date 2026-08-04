<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientesController;
use App\Http\Controllers\Admin\VentasController;
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Admin\VehiculosController;
use App\Http\Controllers\Admin\VehicleSpecificationsController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\InventoryOperationsController;
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
use App\Http\Controllers\ServiceVehicleTypePriceController;
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
Route::patch('/carrito/actualizar/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::delete('/carrito/quitar/{id}', [CarritoController::class, 'quitar'])->name('carrito.quitar');
Route::delete('/carrito/limpiar', [CarritoController::class, 'limpiar'])->name('carrito.limpiar');

// Checkout y pagos publicos (invitado o autenticado)
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/orden/crear', [OrderController::class, 'store'])->name('orden.store');
Route::post('/orden/cajita', [OrderController::class, 'prepareBox'])->name('orden.cajita');
Route::post('/reservas/catalogo', [OrderController::class, 'reservarCatalogo'])->name('reservas.catalogo');
Route::get('/orden/{order}/confirmacion', [OrderController::class, 'confirmacion'])->name('orden.confirmacion');
Route::get('/orden/{order}/comprobante', [OrderController::class, 'comprobante'])->name('orden.comprobante');
Route::get('/orden/{order}/comprobante/descargar', [OrderController::class, 'descargarComprobante'])->name('orden.comprobante.descargar');
Route::get('/transaccion-exitosa', [TransactionController::class, 'success'])->name('transaccion.exitosa');
Route::get('/payphone/success', [TransactionController::class, 'success'])->name('payphone.success');
Route::get('/payphone/cancel', [TransactionController::class, 'cancel'])->name('payphone.cancel');

if (app()->environment('local')) {
    Route::get('/dev/preview/confirmacion', function () {
        $order = \App\Models\Order::query()
            ->with(['items.itemable', 'transaction', 'user'])
            ->latest()
            ->first();

        if (!$order) {
            $order = new \App\Models\Order([
                'user_id' => null,
                'total' => 15.00,
                'status' => 'paid',
                'order_type' => 'purchase',
            ]);
            $order->id = 999999;
            $order->created_at = now();
            $order->updated_at = now();

            $item = new \App\Models\OrderItem([
                'quantity' => 1,
                'unit_price' => 15.00,
            ]);
            $item->setRelation('itemable', new \App\Models\CatalogItem(['name' => 'Lavada Completa']));

            $transaction = new \App\Models\Transaction([
                'payphone_ref' => 'PREVIEW-' . now()->format('YmdHis'),
                'amount' => 15.00,
                'status' => 'approved',
                'client_transaction_id' => 'preview-' . \Illuminate\Support\Str::uuid(),
            ]);

            $order->setRelation('items', collect([$item]));
            $order->setRelation('transaction', $transaction);
            $order->setRelation('user', auth()->user());
        }

        $receipt = app(\App\Services\CheckoutReceiptService::class)->build($order);

        return view('checkout.confirmacion', ['order' => $order, ...$receipt]);
    })->name('dev.preview.confirmacion');

    Route::get('/dev/preview/comprobante', function () {
        $order = \App\Models\Order::query()
            ->with(['items.itemable', 'transaction', 'user'])
            ->latest()
            ->first();

        if (!$order) {
            abort(404, 'Crea una orden o usa primero la previsualizacion de confirmacion.');
        }

        $receipt = app(\App\Services\CheckoutReceiptService::class)->build($order);

        return view('checkout.comprobante', ['order' => $order, ...$receipt]);
    })->name('dev.preview.comprobante');
}

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
    Route::patch('/profile/account', [ProfileController::class, 'updateAccount'])->name('profile.account.update');
    Route::patch('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.security.update');
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
        Route::post('/vehiculos/quick-store', [VehiculosController::class, 'quickStore'])->name('vehiculos.quick-store');
        Route::get('/vehiculos/especificaciones', [VehicleSpecificationsController::class, 'index'])
            ->name('vehiculos.specifications.index');
        Route::post('/vehiculos/especificaciones/relaciones', [VehicleSpecificationsController::class, 'storeSpecification'])
            ->name('vehiculos.specifications.store');
        Route::put('/vehiculos/especificaciones/relaciones/{vehicleSpecification}', [VehicleSpecificationsController::class, 'updateSpecification'])
            ->name('vehiculos.specifications.update');
        Route::delete('/vehiculos/especificaciones/relaciones/{vehicleSpecification}', [VehicleSpecificationsController::class, 'destroySpecification'])
            ->name('vehiculos.specifications.destroy');
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
        Route::get('/inventario/exportar', [InventarioController::class, 'export'])->name('inventario.export');
        Route::get('/inventario/importar', [InventarioController::class, 'import'])->name('inventario.import');
        Route::post('/inventario/importar/preview', [InventarioController::class, 'previewImport'])->name('inventario.import.preview');
        Route::post('/inventario/importar', [InventarioController::class, 'storeImport'])->name('inventario.import.store');
        Route::get('/inventario/reportes', [InventoryOperationsController::class, 'reports'])->name('inventario.reports');
        Route::get('/inventario/reportes/exportar/{section}', [InventoryOperationsController::class, 'exportReport'])->name('inventario.reports.export');
        Route::get('/inventario/cierres', [InventoryOperationsController::class, 'periods'])->name('inventario.periods');
        Route::post('/inventario/cierres', [InventoryOperationsController::class, 'storePeriod'])->name('inventario.periods.store');
        Route::get('/inventario/ubicaciones', [InventoryOperationsController::class, 'locations'])->name('inventario.locations');
        Route::post('/inventario/ubicaciones', [InventoryOperationsController::class, 'storeLocation'])->name('inventario.locations.store');
        Route::get('/inventario/proveedores', [InventoryOperationsController::class, 'suppliers'])->name('inventario.suppliers');
        Route::post('/inventario/proveedores', [InventoryOperationsController::class, 'storeSupplier'])->name('inventario.suppliers.store');
        Route::get('/inventario/compras', [InventoryOperationsController::class, 'purchases'])->name('inventario.purchases');
        Route::post('/inventario/compras', [InventoryOperationsController::class, 'storePurchase'])->name('inventario.purchases.store');
        Route::get('/inventario/transferencias', [InventoryOperationsController::class, 'transfers'])->name('inventario.transfers');
        Route::post('/inventario/transferencias', [InventoryOperationsController::class, 'storeTransfer'])->name('inventario.transfers.store');
        Route::get('/inventario/devoluciones', [InventoryOperationsController::class, 'returns'])->name('inventario.returns');
        Route::post('/inventario/devoluciones', [InventoryOperationsController::class, 'storeReturn'])->name('inventario.returns.store');
        Route::get('/inventario/conteos', [InventoryOperationsController::class, 'counts'])->name('inventario.counts');
        Route::post('/inventario/conteos', [InventoryOperationsController::class, 'storeCount'])->name('inventario.counts.store');
        Route::get('/inventario/kardex/{variant}/exportar', [InventoryOperationsController::class, 'exportKardex'])->name('inventario.kardex.export');
        Route::get('/inventario/kardex/{variant}', [InventoryOperationsController::class, 'kardex'])->name('inventario.kardex');
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
        Route::get('/catalogo/items/{catalogItem}/precios-vehiculo/create', [ServiceVehicleTypePriceController::class, 'create'])
            ->name('catalog-service-prices.create');
        Route::post('/catalogo/items/{catalogItem}/precios-vehiculo', [ServiceVehicleTypePriceController::class, 'store'])
            ->name('catalog-service-prices.store');
        Route::get('/catalogo/precios-vehiculo/{serviceVehicleTypePrice}/edit', [ServiceVehicleTypePriceController::class, 'edit'])
            ->name('catalog-service-prices.edit');
        Route::put('/catalogo/precios-vehiculo/{serviceVehicleTypePrice}', [ServiceVehicleTypePriceController::class, 'update'])
            ->name('catalog-service-prices.update');
        Route::delete('/catalogo/precios-vehiculo/{serviceVehicleTypePrice}', [ServiceVehicleTypePriceController::class, 'destroy'])
            ->name('catalog-service-prices.destroy');
        Route::resource('/catalogo/items', CatalogItemController::class)
            ->parameters(['items' => 'catalogItem'])
            ->except(['index'])
            ->names('catalog-items');
        Route::get('/catalogo/variantes', fn () => redirect()->route('admin.catalog-variants.index'));
        Route::get('/catalogo/variantes/create', fn (\Illuminate\Http\Request $request) => redirect()->route('admin.catalog-variants.create', $request->query()));
        Route::get('/catalogo/variantes/{catalogVariant}/edit', fn (\Illuminate\Http\Request $request, \App\Models\CatalogItemVariant $catalogVariant) => redirect()->route('admin.catalog-variants.edit', ['catalogVariant' => $catalogVariant] + $request->query()));
        Route::get('/catalogo/variantes/{catalogVariant}', fn (\Illuminate\Http\Request $request, \App\Models\CatalogItemVariant $catalogVariant) => redirect()->route('admin.catalog-variants.show', ['catalogVariant' => $catalogVariant] + $request->query()));
        Route::resource('/catalogo/presentaciones', CatalogItemVariantController::class)
            ->parameters(['presentaciones' => 'catalogVariant'])
            ->names('catalog-variants');

        // Empresa
        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
        Route::delete('/empresa/logo', [EmpresaController::class, 'deleteLogo'])->name('empresa.deleteLogo');

        // Landing Banners
        Route::resource('/banners', BannerController::class);

        Route::resource('/orders', OrderController::class)->only(['index', 'show', 'destroy']);
        Route::patch('/orders/{order}/marcar-pagada', [OrderController::class, 'marcarPagada'])->name('orders.marcar-pagada');
        Route::patch('/orders/{order}/estado-operativo', [OrderController::class, 'updateWorkStatus'])->name('orders.work-status');
        Route::patch('/orders/{order}/datos-operativos', [OrderController::class, 'updateOperationalDetails'])->name('orders.operational-details');
        Route::resource('/transactions', TransactionController::class)->only(['index', 'show']);
    });

require __DIR__ . '/auth.php';
