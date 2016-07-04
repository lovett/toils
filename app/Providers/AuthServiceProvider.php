<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider
    as ServiceProvider;

/**
 * Standard Laravel auth service provider class
 */
class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = ['App\Model' => 'App\Policies\ModelPolicy'];


    /**
     * Register any application authentication / authorization services.
     *
     * @param GateContract $gate GateContract instance.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        parent::registerPolicies($gate);
    }
}
