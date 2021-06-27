<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\ForbiddenCharacterException;

/**
 * Provides methods for finding a route
 */
interface RoutesFinderInterface
{
    /**
     * Finds the required route by the HTTP request and returns it along with the variables
     *
     * @param string $urlPath the path to which routes will be matched
     * @param string $method request method to which routes methods will be matched
     * @return RouteInterface|null returns route if found, returns null if route not found
     * @throws ForbiddenCharacterException thrown when an forbidden character is found in the pattern
     */
    public function findRoute(string $urlPath, string $method): ?RouteInterface;

    /**
     * Searches routes by name
     *
     * @param string $routeName the name of the route you want to search
     * @return RouteInterface|null route found, or null if not found
     */
    public function findRouteByName(string $routeName): ?RouteInterface;
}
