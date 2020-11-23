<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Time;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Handle Eloquent events related to Invoices.
 */
class InvoiceObserver
{


    /**
     * Auto-generate an invoice number before creating an invoice
     *
     * @param Invoice $invoice An invoice instance.
     */
    public function creating(Invoice $invoice)
    {
        $invoiceCount = (int) $invoice->project->invoices()->withTrashed()->count();
        $invoice->number = sprintf(
            '%d%03d',
            $invoice->project_id,
            $invoiceCount + 1
        );
    }


    /**
     * Disassociate time entries from a deleted invoice
     *
     * @param Invoice $invoice An invoice instance.
     */
    public function deleted(Invoice $invoice)
    {
        $invoice->time()->update(['invoice_id' => null]);
        $invoice->trashReceipt();
    }

    /**
     * Set date paid if a receipt is present
     *
     * @param Invoice $invoice An invoice instance.
     */
    public function saving(Invoice $invoice)
    {
        if ($invoice->receipt && empty($invoice->paid)) {
            $invoice->paid = new Carbon();
        }
    }

    /**
     * Associate time entries with an invoice
     *
     * @param Invoice $invoice An invoice instance.
     */
    public function saved(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $detachedRowCount = $invoice->time()->update(['invoice_id' => null]);

            $times = Time::whereBetween('start', [$invoice->start, $invoice->end]);
            $times->where('project_id', $invoice->project_id);
            $times->whereNull('invoice_id');
            $attachedRowCount = $times->update(['invoice_id' => $invoice->getKey()]);
            DB::commit();

            $message = sprintf(
                'Detached %d time entries and attached %d to saved invoice %s',
                $detachedRowCount,
                $attachedRowCount,
                $invoice->id
            );

            Log::debug($message);
        });
    }
}
