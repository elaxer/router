<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use PHPUnit\Framework\TestCase;
use Elaxer\Router\{Parser\ForbiddenCharacterException, Router, Route};

/**
 * Class RouterTest
 *
 * @package Router\Tests
 */
class RouterTest extends TestCase
{
    /**
     * @covers Router::findRoute
     * @dataProvider findRouteProvider
     * @dataProvider findRouteNotFoundProvider
     * @param string $urlPath
     * @param string $method
     * @param Route|null $expectedRoute
     * @param array $routes
     * @return void
     * @throws ForbiddenCharacterException
     */
    public function testFindRoute(string $urlPath, string $method, ?Route $expectedRoute, array $routes): void
    {
        $router = new Router();

        foreach ($routes as $route) {
            $router->addRoute($route);
        }

        $routeFound = $router->findRoute($urlPath, $method);

        $this->assertSame($expectedRoute, $routeFound);
    }

    /**
     * @return iterable
     */
    public function findRouteProvider(): iterable
    {
        $expectedRoute = new Route('GET', '/', 'index');
        yield ['/', 'GET', $expectedRoute, [$expectedRoute]];

        $expectedRoute = new Route('DELETE', '/users/{id:\d{2}}', 'deleteUser');
        yield ['/users/12', 'DELETE', $expectedRoute, [
            new Route('GET', '/', 'index'),
            new Route('POST', '/users', 'addUser'),
            $expectedRoute,
        ]];

        $expectedRoute = new Route('GET', '/posts/{id}', 'postHandler');
        yield ['/posts/good-post31_', 'GET', $expectedRoute, [$expectedRoute, new Route('GET', '/posts', 'postsHandler')]];

        $expectedRoute = new Route('DELETE', '/posts/{id:\d+}', 'deletePost');
        yield ['/posts/51', 'DELETE', $expectedRoute, [
            new Route('GET', '/', fn() => 'Hello world'),
            new Route('GET', '/posts/{id:\d+}', fn(int $id) => "Post with id $id"),
            new Route('POST', '/posts', 'createPost'),
            $expectedRoute,
        ]];
    }

    /**
     * @return iterable
     */
    public function findRouteNotFoundProvider(): iterable
    {
        yield ['/', 'GET', null, [
            new Route('GET', '/users', 'usersList'),
            new Route('GET', '/users/{id:\d+}', 'getUser'),
            new Route('POST', '/users', 'createUser'),
        ]];
        yield ['/users/john', 'GET', null, [
            new Route('GET', '/users', 'usersList'),
            new Route('GET', '/users/{id:\d+}', 'getUser'),
            new Route('POST', '/users', 'createUser'),
        ]];
    }

    /**
     * @covers Router::findRoute
     * @return void
     * @throws ForbiddenCharacterException
     */
    public function testFindRouteTwice(): void
    {
        $router = new Router();
        $urlPath = '/news/breaking-news-22_02';
        $method = 'PUT';

        $router->addRoute(new Route('GET', '/', fn() => 'Hello world'));

        $expectedRoute = new Route('PUT', '/news/{id:[a-zA-Z-_0-9]{0,30}}', 'editNewsItem');
        $router->addRoute($expectedRoute);

        $routeFound = $router->findRoute($urlPath, $method);
        $this->assertSame($expectedRoute, $routeFound);

        $router->addRoute(new Route('DELETE', '/news/{id:[a-zA-Z-_0-9]{0,30}}', 'deleteNewsItem'));
        $router->addRoute(new Route('GET', '/news/{id:[a-zA-Z-_0-9]{0,30}}/sources/{sourceId}', 'getNewsItemSource'));
        $router->addRoute(new Route('GET', '/authors', 'authorsList'));

        $routeFound2 = $router->findRoute($urlPath, $method);

        $this->assertSame($expectedRoute, $routeFound2);
    }
}
