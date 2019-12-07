<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Prune
{


    /**
     * Permanently remove soft-deleted records.
     *
     * @param int $days Consider records deleted this many days ago.
     */
    public static function prune(int $days = 7)
    {
        with(new static)::onlyTrashed()
            ->whereRaw("deleted_at < datetime('now', 'start of day', '-{$days} days')")
            ->forceDelete();
    }
}
