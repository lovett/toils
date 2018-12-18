<?php

namespace App\Observers;

use App\Time;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Handle Eloquent events related to Time.
 */
class TimeObserver
{


    /**
     * Unbillable time cannot be attached to an invoice
     *
     * If a time entry is marked as unbillable, disassociate it from any invoice.
     *
     * @param Time $time A Time instance.
     */
    public function saving(Time $time)
    {
        // Laravel doesn't cast the original value like it does the current one.
        if ($time->billable === false) {
            $time->invoice_id = null;
        }
    }
}
