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
    private array $routes = [];

    /**
     * @return array<Route> returns an array of all routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Adds a route to the list
     *
     * @param Route
     * @return void
     * @throws RouteAddingException
     */
    public function addRoute(Route $route): void
    {
        if ($route->getName() !== null) {
            foreach ($this->routes as $routerRoute) {
                if ($route->getName() === $routerRoute->getName()) {
                    throw new RouteAddingException("A route named \"{$route->getName()}\" is already set");
                }
            }
        }

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

    /**
     * Searches routes by name
     *
     * @param string $routeName The name of the route you want to search
     * @return Route|null route found, or null if not found
     */
    public function findRouteByName(string $routeName): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $routeName) {
                return $route;
            }
        }

        return null;
    }
}
