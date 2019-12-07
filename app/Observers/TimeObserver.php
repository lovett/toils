<?php

namespace App\Observers;

use App\Invoice;
use App\Time;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Handle Eloquent events related to time entries.
 */
class TimeObserver
{


    /**
     * Pre-save checks for time entries
     *
     * Unbillable time cannot be attached to an invoice. If a time
     * entry is marked as unbillable, disassociate it from any
     * invoice.
     *
     * @param Time $time A time instance.
     */
    public function saving(Time $time)
    {
        if ($time->billable === false) {
            $time->invoice_id = null;
            return;
        }

        if ($time->finished === false) {
            $time->invoice_id = null;
            return;
        }

        if ($time->isDirty(['start', 'minutes', 'project_id']) === false) {
            return;
        }

        $invoice = Invoice::where([
            ['start', '<=', $time->start],
            ['end', '>=', $time->end],
            ['project_id', $time->project_id],
        ])->first();

        $time->invoice_id = null;
        if ($invoice) {
            $time->invoice_id = $invoice->id;
        }
    }

    /**
     * Delete child records so that the database can be pruned.
     *
     * This is equivalent to having "ON DELETE CASCADE" in the
     * database schema, but handled at the application layer instead
     * for flexibility and to consolidate logic in one place.
     *
     * @param Time $time A time instance.
     */
    public function deleted(Time $time)
    {
        // Deletes from taggables table.
        $time->tags()->delete();
    }
}
