<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('helpdesk', function($user){
            return $user->hasRole('helpdesk');
        });
        Gate::define('admin-helpdesk', function($user){
            return $user->hasAnyRoles(['helpdesk','admin']);
        });

        Gate::define('admin', function($user){
            return $user->hasRole('admin');
        });

        //
    }
}
