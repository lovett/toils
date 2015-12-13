<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $dbDefault = $this->app['config']['database.default'];

        $connections = $this->app['config']['database.connections'];

        $defaultConnection = $connections[$dbDefault];

        if ($defaultConnection['driver'] !== 'sqlite') {
            return;
        }

        if (file_exists($defaultConnection['database'])) {
            return;
        }

        touch($defaultConnection['database']);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
