<?php

namespace App\Observers;

use App\Models\User;

/**
 * Handle Eloquent events related to Users.
 */
class UserObserver
{


    /**
     * Delete child records so that the database can be pruned.
     *
     * This is roughly equivalent to having "ON DELETE CASCADE" in the
     * database schema, but handled at the application layer instead
     * for flexibility.
     *
     * @param User $user A user instance.
     */
    public function deleted(User $user)
    {
        $user->time()->delete();
    }
}
