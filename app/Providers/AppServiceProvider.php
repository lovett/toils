<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Observers\InvoiceObserver;
use App\Observers\ClientObserver;
use App\Observers\EstimateObserver;
use App\Observers\ProjectObserver;
use App\Observers\TimeObserver;
use App\Observers\UserObserver;
use App\Observers\Observer;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Estimate;
use App\Models\Project;
use App\Models\Time;
use App\Models\User;

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
        Invoice::observe(InvoiceObserver::class);
        Client::observe(ClientObserver::class);
        Estimate::observe(EstimateObserver::class);
        Project::observe(ProjectObserver::class);
        Time::observe(TimeObserver::class);
        User::observe(UserObserver::class);
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
