<?php

declare(strict_types=1);

namespace Elaxer\Router;

/**
 * Routes factory
 */
interface RoutesFactoryInterface
{
    /**
     * Factory method for creating routes
     *
     * @param array|null $methods route method. Can be null if the route doesn't care which method
     * @param string $pattern route pattern. May contain regular expressions in braces "{}"
     * @param mixed $handler route handler. May contain any type of value
     * @param string|null $name route name, identifier
     * @return RouteInterface created route
     */
    public function createRoute(
        ?array $methods,
        string $pattern,
        $handler,
        ?string $name = null
    ): RouteInterface;
}
