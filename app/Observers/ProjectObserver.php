<?php

namespace App\Observers;

use App\Models\Project;

/**
 * Handle Eloquent events related to Projects.
 */
class ProjectObserver
{


    /**
     * Update foreign key relations to allow for pruning.
     *
     * This is equivalent to having "ON DELETE CASCADE" in the database schema,
     * but handled at the application layer instead for flexibility.
     *
     * @param Project $project A project instance.
     */
    public function deleted(Project $project): void
    {
        $project->time()->delete();
        $project->invoices()->delete();
    }
}
