<?php

namespace App\Observers;

use App\Client;
use App\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Handle Eloquent events related to Clients.
 */
class ClientObserver
{


    /**
     * Mirror a client's active-ness at the project level
     *
     * If a client becomes inactive, their projects should also become
     * inactive. But not vice-versa.
     *
     * @param Client $client A client instance.
     */
    public function saved(Client $client)
    {
        // Laravel doesn't cast the original value like it does the current one.
        $previouslyActive = (bool) $client->getOriginal('active');

        if ($client->active === false && $previouslyActive === true) {
            Log::debug("Marking a newly-inactive client's projects as inactive");
            $client->projects()->update(['active' => false]);
        }
    }
}
