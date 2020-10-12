<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('manage-users', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('manage-transactions', function ($user) {
            return $user->hasRole('coordinator venezuelan_operator');
        });
        Gate::define('manage-settings', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('manage-rates', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('create-transaction', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('manage-currencies', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('manage-banks', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('manage-accounts', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('approve-operations', function ($user) {
            return $user->hasRole('coordinator');
        });
        Gate::define('see-all-transactions',function($user){
            return $user->hasRole('coordinator');
        });
        Gate::define('see-my-transactions',function($user){
            return $user->hasRole('coordinator');
        });
      Gate::define('operate-venezuelan-transactions',function($user){
        return $user->hasRole('coordinator venezuelan_operator');
      });


        Passport::routes();

        //
    }
}
