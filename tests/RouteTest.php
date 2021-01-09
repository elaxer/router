<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use PHPUnit\Framework\TestCase;
use Elaxer\Router\Route;

/**
 * Class RouteTest
 *
 * @package Router\Tests
 */
class RouteTest extends TestCase
{
    /**
     * @covers Route::getMethods
     * @covers Route::getPattern
     * @covers Route::getHandler
     * @return void
     */
    public function testConstructor(): void
    {
        $route = new Route(['GET'], '/', 'HomeController@index');

        $this->assertEquals('/', $route->getPattern());
        $this->assertEquals('HomeController@index', $route->getHandler());
        $this->assertEquals(['GET'], $route->getMethods());
    }
}
