<?php

namespace App\Providers;

use App\Models\Empresa;
use Throwable;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Order;
use App\Policies\OrderPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);

        try {
            $empresa = Schema::hasTable('empresas')
                ? (Empresa::first() ?? new Empresa())
                : new Empresa();
        } catch (Throwable) {
            $empresa = new Empresa();
        }

        View::share('empresa', $empresa);
    }
}
