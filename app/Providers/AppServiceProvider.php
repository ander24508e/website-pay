<?php

namespace App\Providers;

use App\Models\Empresa;
use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        if (app()->environment('production')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::before(function ($user) {
            return $user->isOwner() ? true : null;
        });

        try {
            if (Schema::hasTable('empresas')) {
                $query = Empresa::query();

                if (Schema::hasTable('landing_banners')) {
                    $query->with('landingBanners');
                }

                $empresa = $query->first() ?? new Empresa;
            } else {
                $empresa = new Empresa;
            }
        } catch (Throwable) {
            $empresa = new Empresa;
        }

        View::share('empresa', $empresa);
    }
}
