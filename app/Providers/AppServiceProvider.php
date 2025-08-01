<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        /**
         * Con questa riga si dice a Laravel che, nel caso si scelga di renderizzare la paginazione di default,
         * deve usare il framework HTML Bootstrap 5.
         */
        Paginator::useBootstrapFive();
    }
}
