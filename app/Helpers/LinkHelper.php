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
        $resource = static::firstRouteSegment();
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
     * Render a link as a button.
     *
     * @param string $route   The route to link to
     * @param string $label   The text of the link
     * @param array  $params  Querystring parameters to include with the link
     * @param array  $attribs Additional attributes for the link tag
     */
    public static function buttonLink(string $route, string $label, array $params = [], array $attribs = [])
    {
        $class = 'btn btn-primary';

        $attribs['class'] = $class;
        if (array_key_exists('class', $attribs)) {
            $attribs['class'] = $class . ' ' . $attribs['class'];
        }

        return link_to_route($route, $label, $params, $attribs);
    }


    /**
     * Render a link as a small button.
     *
     * @param string $route   The route to link to
     * @param string $label   The text of the link
     * @param array  $params  Querystring parameters to include with the link
     * @param array  $attribs Additional attributes for the link tag
     */
    public static function smallButtonLink(string $route, string $label, array $params = [], array $attribs = [])
    {
        $attribs['class'] = 'btn-sm';
        return static::buttonLink($route, $label, $params, $attribs);
    }

    /**
     * Render a link within a card
     *
     * @param string $route   The route to link to
     * @param string $label   The text of the link
     * @param array  $params  Querystring parameters to include with the link
     * @param array  $attribs Additional attributes for the link tag
     */
    public static function cardLink(string $route, string $label, array $params = [], array $attribs = [])
    {
        $attribs['class'] = 'card-link';
        return link_to_route($route, $label, $params, $attribs);
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
        } else if (Request::routeIs($route)) {
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

    /**
     * Whether the view should render a subnav
     *
     * Uses the length of the current route to decide whether standard
     * CRUD-oriented navigation should be displayed.
     *
     * @return boolean
     */
    public static function showSubnav()
    {
        $routeSegments = explode('.', Route::currentRouteName());

        return count($routeSegments) > 1;
    }

    /**
     * Generic navigation for CRUD-based operations
     *
     * Infers standard links such as list, create, based on the current route
     * and standard resource controller actions.
     *
     * @return array
     */
    public static function getSubnav()
    {
        $action = Route::getCurrentRoute()->getActionName();
        $params = Route::getCurrentRoute()->parameters;

        $resource = static::firstRouteSegment();
        $capitalizedResource = ucfirst($resource);

        $links = [];

        if ($resource === 'password') {
            return $links;
        }

        if ($resource === 'project') {
            $links[] = self::projectsIndexLink();
            $links[] = self::inactiveProjectsLink();
        }

        if ($resource === 'client') {
            $links[] = self::clientsIndexLink();
            $links[] = self::inactiveClientsLink();
        }

        if (empty($links)) {
            $links[] = self::navLink("{$resource}.index", "{$capitalizedResource} List", []);
        }

        if (strpos($action, '@show') !== false || strpos($action, '@edit') !== false) {
            $links[] = self::navLink("{$resource}.show", "{$capitalizedResource} Overview", $params);
            $links[] = self::navLink("{$resource}.edit", "Edit {$capitalizedResource}", $params);
        }

        $links[] = self::navLink("{$resource}.create", "New {$capitalizedResource}", []);

        return $links;
    }

    /**
     * Return the first segment of the current route.
     */
    public static function firstRouteSegment()
    {
        $routeSegments = explode('.', Route::currentRouteName());
        return $routeSegments[0];
    }

    /**
     * The default list view of the project list is a preset search.
     */
    public static function projectsIndexLink()
    {
        return self::primaryNavLink(
            'project.index',
            'Projects',
            ['q' => 'status:active']
        );
    }

    /**
     * The default list view of the client list is a preset search.
     */
    public static function clientsIndexLink()
    {
        return self::primaryNavLink(
            'client.index',
            'Clients',
            ['q' => 'status:active']
        );
    }

    /**
     * Secondary view of the project list limited to inactive projects.
     */
    public static function inactiveProjectsLink()
    {
        return self::navLink(
            'project.index',
            'Inactive Projects',
            ['q' => 'status:inactive']
        );
    }

    /**
     * Secondary view of the client list limited to inactive projects.
     */
    public static function inactiveClientsLink()
    {
        return self::navLink(
            'client.index',
            'Inactive Clients',
            ['q' => 'status:inactive']
        );
    }
}
