<?php

declare(strict_types=1);

namespace Elaxer\Router;

class RoutesCollection implements RoutesCollectionInterface
{
    /**
     * @var Route[]
     */
    protected array $routes = [];

    /**
     * {@inheritDoc}
     */
    public function addRoute(Route $route): self
    {
        if ($route->getName() !== null) {
            foreach ($this->routes as $routerRoute) {
                if ($route->getName() === $routerRoute->getName()) {
                    throw new RouteAddingException("A route named \"{$route->getName()}\" is already set");
                }
            }
        }

        $this->routes[] = $route;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }
}
