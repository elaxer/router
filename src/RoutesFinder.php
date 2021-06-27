<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\PatternParserInterface;

/**
 * Implementation of search for routes
 */
class RoutesFinder implements RoutesFinderInterface
{
    /**
     * @var RoutesCollectionInterface routes collection
     */
    protected RoutesCollectionInterface $routesCollection;

    /**
     * @var PatternParserInterface pattern parser
     */
    protected PatternParserInterface $patternParser;

    /**
     * @param RoutesCollectionInterface $routesCollection routes collection
     * @param PatternParserInterface $patternParser pattern parser
     */
    public function __construct(RoutesCollectionInterface $routesCollection, PatternParserInterface $patternParser)
    {
        $this->routesCollection = $routesCollection;
        $this->patternParser = $patternParser;
    }

    /**
     * {@inheritDoc}
     */
    public function findRoute(string $urlPath, string $method): ?RouteInterface
    {
        foreach ($this->routesCollection->getAllRoutes() as $route) {
            if ($route->getMethods() !== null && !in_array($method, $route->getMethods())) {
                continue;
            }

            $regexp = $this->patternParser->makeRegexpFromPattern(
                $route->getPattern(),
                $this->patternParser->getParameters($route->getPattern())
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
    public function findRouteByName(string $routeName): ?RouteInterface
    {
        foreach ($this->routesCollection->getAllRoutes() as $route) {
            if ($route->getName() === $routeName) {
                return $route;
            }
        }

        return null;
    }
}
