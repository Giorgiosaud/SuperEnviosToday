<?php

namespace App\Providers;

use App\Auth\PasswordBrokerManager;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider as LaravelPasswordResetServiceProvider;

class PasswordResetServiceProvider extends LaravelPasswordResetServiceProvider
{
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });
    }

}
