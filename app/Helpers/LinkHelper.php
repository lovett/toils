<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/**
 * Helper functions for displaying links.
 */
class LinkHelper
{


    /**
     * Render a primary navigation link with awareness of the current route
     *
     * If the route matches resource of the current route, the link is
     * displayed in the active state.
     *
     * @param string $route   The route to link to
     * @param string $label   The text of the link
     * @param array  $params  Querystring parameters to include with the link
     * @param array  $attribs Additional attributes for the link tag
     *
     * @return string
     */
    public static function primaryNavLink(string $route, string $label, array $params = [], array $attribs = [])
    {
        $routeSegments = explode('.', Route::currentRouteName());
        $resource = $routeSegments[0];

        $linkedRoute = explode('.', $route);

        $liClass = 'nav-item';
        $linkClass = 'nav-link';
        if ($linkedRoute[0] === $resource) {
            $liClass .= ' bg-primary';
            $linkClass .= ' text-light';
        }

        if (isset($attribs['class'])) {
            $linkClass .= ' ' . $attribs['class'];
        }

        $attribs['class'] = $linkClass;

        return sprintf(
            '<li class="%s">%s</li>',
            $liClass,
            link_to_route($route, $label, $params, $attribs)
        );
    }

    /**
     * Render a non-primary navigation link with awareness of current route
     *
     * If the route matches the current route, the link is displayed
     * in the active state.
     *
     * @param string $route   The route to link to
     * @param string $label   The text of the link
     * @param array  $params  Querystring parameters to include with the link
     * @param array  $attribs Additional attributes for the link tag
     *
     * @return string
     */
    public static function navLink(string $route, string $label, array $params = [], array $attribs = [])
    {
        $liClass = 'nav-item';
        $linkClass = 'nav-link';

        if (array_key_exists('q', $params)) {
            $searchQuery = Request::query('q');
            if (strpos($searchQuery, $params['q']) !== false) {
                $linkClass .= ' active';
            }
        } elseif (Request::routeIs($route)) {
            $linkClass .= ' active';
        }

        if (isset($attribs['class'])) {
            $linkClass .= ' ' . $attribs['class'];
        }

        $attribs['class'] = $linkClass;

        return sprintf(
            '<li class="%s">%s</li>',
            $liClass,
            link_to_route($route, $label, $params, $attribs)
        );
    }
}
