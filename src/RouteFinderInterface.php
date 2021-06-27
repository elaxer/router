<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\ForbiddenCharacterException;

interface RouteFinderInterface
{
    /**
     * Finds the required route by the HTTP request and returns it along with the variables
     *
     * @param string $urlPath the path to which routes will be matched
     * @param string $method request method to which routes methods will be matched
     * @return Route|null returns route if found, returns null if route not found
     * @throws ForbiddenCharacterException
     */
    public function findRoute(string $urlPath, string $method): ?Route;

    /**
     * Searches routes by name
     *
     * @param string $routeName The name of the route you want to search
     * @return Route|null route found, or null if not found
     */
    public function findRouteByName(string $routeName): ?Route;
}
