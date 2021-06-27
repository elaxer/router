<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use Elaxer\Router\{PatternParser\PatternParser,
    RouteAddingException,
    RoutesCollection,
    RoutesFactory,
    RoutesFactoryInterface,};
use PHPUnit\Framework\TestCase;

/**
 * @see RoutesCollection
 */
class RoutesCollectionTest extends TestCase
{
    private RoutesFactoryInterface $routesFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->routesFactory = new RoutesFactory(new PatternParser());
    }

    /**
     * Tests adding a route to a collection where a route with the same name already exists
     *
     * @covers RoutesCollection::addRoute
     * @throws RouteAddingException
     */
    public function testAddRouteWithExistedName(): void
    {
        $this->expectException(RouteAddingException::class);
        $this->expectExceptionMessage('A route named "routeName" is already set');

        (new RoutesCollection())
            ->addRoute($this->routesFactory->createRoute(['GET'], '/', null, 'routeName'))
            ->addRoute($this->routesFactory->createRoute(['POST', 'PUT'], '/posts/1', 'updatePost', 'routeName'));
    }
}
