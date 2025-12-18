<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Interfaces\OrderRepositoryInterface::class,
            \App\Repositories\EloquentOrderRepository::class
        );

        $this->app->bind(
            \App\Interfaces\ProductRepositoryInterface::class,
            \App\Repositories\EloquentProductRepository::class
        );

        // #BINDING-HOOK#
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
