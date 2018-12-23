<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Observers\InvoiceObserver;
use App\Observers\ClientObserver;
use App\Observers\TimeObserver;
use App\Invoice;
use App\Client;
use App\Time;

/**
 * Standard Laravel provider class for application setup.
 */
class AppServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        setlocale(LC_MONETARY, 'en_US.UTF-8');

        Invoice::observe(InvoiceObserver::class);
        Client::observe(ClientObserver::class);
        Time::observe(TimeObserver::class);

        Blade::if('notempty', function ($value) {
            return empty($value) === false;
        });
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
