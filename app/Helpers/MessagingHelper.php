<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

/**
 * Helper functions for displaying user messages
 *
 * The value of the userMessageType key should be one of:
 * success, info, warning, danger.
 *
 * @see https://getbootstrap.com/docs/3.3/components/#alerts
 */
class MessagingHelper
{
    public static function flashCreated($label='') {
        $resource = ucfirst(LinkHelper::firstRouteSegment());
        Session::flash('userMessage', "{$resource} {$label} has been created.");
        Session::flash('userMessageType', 'success');
    }

    public static function flashUpdated($label='') {
        $resource = ucfirst(LinkHelper::firstRouteSegment());
        Session::flash('userMessage', "{$resource} {$label} has been updated.");
        Session::flash('userMessageType', 'success');
    }

    public static function flashDeleted($label='') {
        $resource = ucfirst(LinkHelper::firstRouteSegment());
        Session::flash('userMessage', "{$resource} {$label} has been deleted.");
        Session::flash('userMessageType', 'success');
    }

}
