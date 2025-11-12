<?php

namespace App\Providers;

use App\Services\DiscountService;
use App\Services\OrderService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Enregistrement du DiscountService
        $this->app->singleton(DiscountService::class, function ($app) {
            return new DiscountService();
        });

        // Enregistrement du OrderService avec injection du DiscountService
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService($app->make(DiscountService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
