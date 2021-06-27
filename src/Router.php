<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\PatternParser;

/**
 * @package Router
 */
class Router implements RouteFinderInterface
{
    protected RoutesCollectionInterface $routesCollection;

    public function __construct(RoutesCollectionInterface $routesCollection)
    {
        $this->routesCollection = $routesCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function findRoute(string $urlPath, string $method): ?Route
    {
        foreach ($this->routesCollection->getAllRoutes() as $route) {
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
     * {@inheritDoc}
     */
    public function findRouteByName(string $routeName): ?Route
    {
        foreach ($this->routesCollection->getAllRoutes() as $route) {
            if ($route->getName() === $routeName) {
                return $route;
            }
        }

        return null;
    }
}
