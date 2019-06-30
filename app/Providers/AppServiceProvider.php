<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //if ($this->app->environment() !== 'production') {
        //$this->app->register(IdeHelperServiceProvider::class);
        //}

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::defaultView('vendor.pagination.bootstrap-4');
        Schema::defaultStringLength(191);
        //User::observe(UserObserver::class);

        //
    }
}
