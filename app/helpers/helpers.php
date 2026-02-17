<?php

if (!function_exists('isActive')) {
    function isActive($routes)
    {
        $routes = (array) $routes;

        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return 'active';
            }
        }

        return '';
    }
}
