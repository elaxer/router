<?php

namespace Elaxer\Router\Tests;

use Elaxer\Router\Route;
use Elaxer\Router\RouteAddingException;
use Elaxer\Router\RoutesCollection;
use PHPUnit\Framework\TestCase;

class RoutesCollectionTest extends TestCase
{
    /**
     * @covers RoutesCollection::addRoute
     * @throws RouteAddingException
     */
    public function testAddRouteWithExistedName(): void
    {
        $this->expectException(RouteAddingException::class);
        $this->expectExceptionMessage('A route named "routeName" is already set');

        (new RoutesCollection())
            ->addRoute(new Route(['GET'], '/', null, 'routeName'))
            ->addRoute(new Route(['POST', 'PUT'], '/posts/1', 'updatePost', 'routeName'));
    }
}
