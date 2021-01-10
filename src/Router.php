<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\{ForbiddenCharacterException, PatternParser};

/**
 * @package Router
 */
class Router
{
    /**
     * @var array<Route> array of routes
     */
    protected array $routes = [];

    /**
     * Adds a route to the list
     *
     * @param Route
     * @return void
     */
    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Finds the required route by the HTTP request and returns it along with the variables
     *
     * @param string $urlPath the path to which routes will be matched
     * @param string $method request method to which routes methods will be matched
     * @return Route|null returns route if found, returns null if route not found
     * @throws ForbiddenCharacterException
     */
    public function findRoute(string $urlPath, string $method): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getMethods() !== null && !in_array($method, $route->getMethods())) {
                continue;
            }

            $regexp = PatternParser::makeRegexpFromPattern(
                $route->getPattern(),
                PatternParser::getParameters($route->getPattern())
            );

            if (preg_match($regexp, $urlPath)) {
                return $route;
            }
        }

        return null;
    }
}
