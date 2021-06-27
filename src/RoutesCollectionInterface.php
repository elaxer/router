<?php

declare(strict_types=1);

namespace Elaxer\Router;

/**
 * Provides methods for storing routes
 */
interface RoutesCollectionInterface
{
    /**
     * Adds a route to the collection
     *
     * @param RouteInterface $route added route
     * @return self
     * @throws RouteAddingException Thrown out if a route is added with a name that is already in the collection
     */
    public function addRoute(RouteInterface $route): self;

    /**
     * Returns all routes added to the collection
     *
     * @return RouteInterface[]
     */
    public function getAllRoutes(): array;
}
