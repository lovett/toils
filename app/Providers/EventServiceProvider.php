<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider
    as ServiceProvider;
use App\Invoice;
use App\Time;

/**
 * Standard Laravel event service provider class.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => ['App\Listeners\EventListener'],
    ];

    /**
     * Register any other events for your application.
     *
     * @param DispatcherContract $events DispatcherContract instance
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        Time::saved(function ($model) {
            $model->attachInvoice();
            return true;
        });

        Invoice::saved(function ($model) {
            $model->attachTime();
            return true;
        });
    }
}
