<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

/**
 * Standard Laravel provider class for broadcast.
 */
class BroadcastServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        // phpcs:disable PEAR.Files.IncludingFile.UseInclude
        require base_path('routes/channels.php');
    }
}
