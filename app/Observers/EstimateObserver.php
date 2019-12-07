<?php

namespace App\Observers;

use App\Estimate;

/**
 * Handle Eloquent events related to Estimates.
 */
class EstimateObserver
{


    /**
     * Update foreign key relations to allow for pruning.
     *
     * This is roughly equivalent to having "ON DELETE CASCADE" in the
     * database schema, but handled at the application layer instead
     * for flexibility.
     *
     * @param Estimate $estimate A estimate instance.
     */
    public function deleted(Estimate $estimate)
    {
        // Deletes from estimate_user table.
        $estimate->users()->detach();
    }
}
