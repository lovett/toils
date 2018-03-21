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
     * @param string $section The current section declared by the controller
     * @param string $route   The route to link to
     * @param string $label   The text of the link
     * @param array  $params  Querystring parameters to include with the link
     * @param array  $attribs Additional attributes for the link tag
     *
     * @return string
     */
    public static function primaryNavLink($route, $label, array $params = [], array $attribs = []) {
        $resource = static::firstRouteSegment();
        $linkedRoute = explode('.', $route);
        $liClass = ($linkedRoute[0] === $resource) ? 'active' : '';

        return sprintf(
            '<li class="%s">%s</li>',
            $liClass,
            link_to_route($route, $label, $params, $attribs)
        );
    }

    public static function buttonLink($route, $label, array $params = [], array $attribs = [])
    {
        $class = 'btn btn-primary';
        if (array_key_exists('class', $attribs)) {
            $attribs['class'] = $class . ' ' . $attribs['class'];
        } else {
            $attribs['class'] = $class;
        }
        return link_to_route($route, $label, $params, $attribs);
    }

    public static function smallButtonLink($route, $label, array $params = [], array $attributes = [])
    {
        $attributes['class'] = 'btn-sm';
        return static::buttonLink($route, $label, $params, $attributes);
    }

    public static function extraSmallButtonLink($route, $label, array $params = [], array $attributes = [])
    {
        $attributes['class'] = 'btn-xs';
        return static::buttonLink($route, $label, $params, $attributes);
    }

    /**
     * Render a non-primary navigation link with awareness of current route
     *
     * If the route matches the current route, the link is displayed
     * in the active state.
     *
     * @param string $section The current section declared by the controller
     * @param string $route   The route to link to
     * @param string $label   The text of the link
     * @param array  $params  Querystring parameters to include with the link
     * @param array  $attribs Additional attributes for the link tag
     *
     * @return string
     */
    public static function navLink($route, $label, array $params = [], array $attribs = []) {
        $liClass = Request::routeIs($route) ? 'active' : '';

        return sprintf(
            '<li class="%s">%s</li>',
            $liClass,
            link_to_route($route, $label, $params, $attribs)
        );
    }

    /**
     * Whether the view should render a subnav
     *
     * Uses the length of the current route to decide whether standard
     * CRUD-oriented navigation should be displayed.
     *
     * @return boolean
     */
    public static function showSubnav() {
        $routeSegments = explode('.', Route::currentRouteName());

        return sizeof($routeSegments) > 1;
    }

    /**
     * Generic navigation for CRUD-based operations
     *
     * Infers standard links such as list, create, based on the current route
     * and standard resource controller actions.
     *
     * @return array
     */
    public static function getSubnav() {
        $action = Route::getCurrentRoute()->getActionName();
        $params = Route::getCurrentRoute()->parameters;

        $resource = static::firstRouteSegment();
        $capitalizedResource = ucfirst($resource);

        $links = [
            LinkHelper::navLink("{$resource}.index", "{$capitalizedResource} List", []),
        ];

        if (strpos($action, '@show') !== false || strpos($action, '@edit') !== false) {
            $links[] = LinkHelper::navLink("{$resource}.show", "{$capitalizedResource} Overview", $params);
            $links[] = LinkHelper::navLink("{$resource}.edit", "Edit {$capitalizedResource}", $params);
        }

        $links[] = LinkHelper::navLink("{$resource}.create", "New {$capitalizedResource}", []);

        return $links;
    }

    public static function firstRouteSegment()
    {
        $routeSegments = explode('.', Route::currentRouteName());
        return $routeSegments[0];
    }
}
