<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

/**
 * Helper functions for displaying user messages
 *
 * The value of the userMessageType key should be one of:
 * success, info, warning, danger.
 */
class MessagingHelper
{


    /**
     * Flash a standard message to the session about record creation.
     *
     * @param string $label The name of the newly-created record type.
     */
    public static function flashCreated(string $label = ''): void
    {
        $label = ucfirst($label);
        Session::flash('userMessage', "{$label} has been created.");
        Session::flash('userMessageType', 'success');
    }


    /**
     * Flash a standard message to the session about a record being successfully updated.
     *
     * @param string $label The name of the newly-updated record type.
     */
    public static function flashUpdated(string $label = ''): void
    {
        $label = ucfirst($label);
        Session::flash('userMessage', "{$label} has been updated.");
        Session::flash('userMessageType', 'success');
    }


    /**
     * Flash a standard message to the session about a record being successfully deleted.
     *
     * @param string $label The name of the newly-updated record type.
     */
    public static function flashDeleted(string $label = ''): void
    {
        $label = ucfirst($label);
        Session::flash('userMessage', "{$label} has been deleted.");
        Session::flash('userMessageType', 'success');
    }
}
