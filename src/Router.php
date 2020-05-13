<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Psr\Http\Message\RequestInterface;
use Elaxer\Router\Parser\{InvalidCharacterException, PatternParser};

/**
 * Class Router
 *
 * @package Router
 */
class Router
{
    /**
     * @var array array of routes
     */
    protected array $routes = [];

    /**
     * Adds a route to the list
     *
     * @param string $method route method
     * @param string $pattern regexp route pattern
     * @param mixed $handler handler for this route
     * @return Route created route
     */
    public function addRoute(string $method, string $pattern, $handler): Route
    {
        $route = new Route($method, $pattern, $handler);
        $this->routes[] = $route;

        return $route;
    }

    /**
     * Finds the required route by the HTTP request and returns it along with the variables
     *
     * @param RequestInterface $request PSR-7 HTTP request
     * @return array associative array, where "route" is the route found,
     * and "Wars" are variables extracted from the request URI
     * @throws InvalidCharacterException
     * @throws RouteNotFoundException
     */
    public function findRoute(RequestInterface $request): array
    {
        foreach ($this->routes as $route) {
            if ($route->method !== $request->getMethod()) {
                continue;
            }

            $parameters = PatternParser::getParameters($route->pattern);
            $regexp = PatternParser::makeRegexpFromPattern($route->pattern, $parameters);

            $matches = [];
            if (preg_match($regexp, $request->getUri()->getPath(), $matches)) {
                $vars = array_filter($matches, fn ($name) => !is_int($name), ARRAY_FILTER_USE_KEY);

                return ['route' => $route, 'vars' => $vars];
            }
        }

        throw new RouteNotFoundException('Route not found');
    }
}
