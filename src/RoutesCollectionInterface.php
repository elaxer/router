<?php

declare(strict_types=1);

namespace Elaxer\Router;

interface RoutesCollectionInterface
{
    /**
     * @param Route $route
     * @return RoutesCollectionInterface
     * @throws RouteAddingException
     */
    public function addRoute(Route $route): self;

    /**
     * @return Route[]
     */
    public function getAllRoutes(): array;
}
