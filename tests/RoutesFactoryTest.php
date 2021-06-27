<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use Elaxer\Router\{PatternParser\PatternParser, RoutesFactory};
use PHPUnit\Framework\TestCase;

/**
 * @see RoutesFactory
 */
class RoutesFactoryTest extends TestCase
{
    /**
     * Tests route creation
     *
     * @covers RoutesFactory::createRoute
     */
    public function testCreateRoute(): void
    {
        $route = (new RoutesFactory(new PatternParser()))->createRoute(['GET'], '/', 'HomeController@index');

        $this->assertEquals('/', $route->getPattern());
        $this->assertEquals('HomeController@index', $route->getHandler());
        $this->assertEquals(['GET'], $route->getMethods());
    }
}
