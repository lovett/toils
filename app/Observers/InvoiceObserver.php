<?php

namespace App\Observers;

use App\Invoice;
use App\Time;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InvoiceObserver
{
    /**
     * Disassociate time entries from a deleted invoice
     *
     * @param  Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        $affectedRows = Time::forInvoice($invoice)->update([
            'invoice_id' => null,
        ]);

        Log::debug("Released ${affectedRows} time entries from deleted invoice {$invoice->id}");
    }

    /**
     * Associate time entries with an invoice
     *
     * @param  Invoice  $invoice
     * @return void
     */
    public function saved(Invoice $invoice)
    {

        DB::transaction(function () use ($invoice) {
            $detachedRows = Time::forInvoice($invoice)->update([
                'invoice_id' => null,
            ]);

            $times = Time::whereBetween('start', [$invoice->start, $invoice->end]);
            $times->where('project_id', $invoice->project_id);
            $times->whereNull('invoice_id');
            $attachedRows = $times->update(['invoice_id' => $invoice->getKey()]);
            DB::commit();

            Log::debug("Detached ${detachedRows} time entries and attached ${attachedRows} to saved invoice {$invoice->id}");
        });
    }
}
