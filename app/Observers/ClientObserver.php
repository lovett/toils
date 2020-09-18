<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\Project;
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

    /**
     * Delete child records so that the database can be pruned.
     *
     * This is roughly equivalent to having "ON DELETE CASCADE" in the
     * database schema, but handled at the application layer instead
     * for flexibility and to consolidate logic in one place.
     *
     * Deletion order is significant. Time and invoice relations depend
     * on a working project relation.
     *
     * @param Client $client A client instance.
     */
    public function deleted(Client $client)
    {
        $client->time()->delete();
        $client->invoices()->delete();

        $client->projects()->delete();

        $client->estimates()->update(['client_id' => null]);
    }
}
