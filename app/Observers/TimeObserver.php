<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Time;
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
    public function saving(Time $time): void
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
}
