<?php

namespace App\Helpers;

/**
 * Helper functions for displaying links
 */
class LinkHelper
{


    /**
     * Render a route link with active class
     *
     * @param string $section The current section declared by the controller.
     * @param string $route   The route to link to.
     * @param string $label   The text of the link.
     * @param array  $params  Querystring parameters to include with the link.
     * @param array  $attribs Additional attributes for the link tag.
     *
     * @return string
     */
    public static function navLink(
        $section,
        $route,
        $label,
        array $params = [],
        array $attribs = []
    ) {
        $class = null;

        $routeBase = explode('.', $route);

        if ($routeBase[0] === $section) {
            $class = 'active';
        }

        if (array_key_exists('class', $attribs) === false) {
            $attribs['class'] = '';
        }

        $attribs['class'] .= ' ' . $class;

        return link_to_route($route, $label, $params, $attribs);
    }
}
