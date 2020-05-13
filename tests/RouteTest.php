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
     * @return void
     */
    public function testConstructor(): void
    {
        $route = new Route('GET', '/', 'HomeController@index');

        $this->assertEquals('GET', $route->method);
        $this->assertEquals('/', $route->pattern);
        $this->assertEquals('HomeController@index', $route->handler);
    }
}
