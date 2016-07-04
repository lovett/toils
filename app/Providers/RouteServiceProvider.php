<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider
    as ServiceProvider;

/**
 * Standard Laravel route service provider class
 */
class RouteServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param Router $router Router instance.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);
    }


    /**
     * Define the routes for the application.
     *
     * @param Router $router Router instance.
     *
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(
            ['namespace' => $this->namespace],
            function ($router) {
                include app_path('Http/routes.php');
            }
        );
    }
}
