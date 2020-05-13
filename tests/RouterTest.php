<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Elaxer\Router\{Parser\InvalidCharacterException, Router, Route, RouteNotFoundException};
use Nyholm\Psr7\ServerRequest;

/**
 * Class RouterTest
 *
 * @package Router\Tests
 */
class RouterTest extends TestCase
{

    /**
     * @dataProvider findRouteProvider
     * @param array $routes
     * @param RequestInterface $request
     * @param array $expectedResult
     * @return void
     * @throws RouteNotFoundException
     * @throws InvalidCharacterException
     */
    public function testFindRoute(array $routes, RequestInterface $request, array $expectedResult): void
    {
        $router = new Router();

        foreach ($routes as $route) {
            $router->addRoute($route->method, $route->pattern, $route->handler);
        }

        $result = $router->findRoute($request);

        $this->assertEquals($expectedResult, ['route' => $result['route'], 'vars' => $result['vars']]);
    }

    /**
     * @return array|array[]
     */
    public function findRouteProvider(): array
    {
        return [
            [
                [new Route('GET', '/', 'index')],
                new ServerRequest('GET', '/'),
                [
                    'route' => new Route('GET', '/', 'index'),
                    'vars' => []
                ]
            ],
            [
                [
                    new Route('GET', '/', 'index'),
                    new Route('POST', '/users', 'addUser'),
                    new Route('DELETE', '/users/{id:\d{2}}', 'deleteUser')
                ],
                new ServerRequest('DELETE', '/users/12'),
                [
                    'route' => new Route('DELETE', '/users/{id:\d{2}}', 'deleteUser'),
                    'vars' => ['id' => 12]
                ]
            ],
            [
                [new Route('GET', '/books/{name}', 'getBook')],
                new ServerRequest('GET', '/books/kafka-metamorphosis'),
                [
                    'route' => new Route('GET', '/books/{name}', 'getBook'),
                    'vars' => ['name' => 'kafka-metamorphosis']
                ]
            ],
            [
                [
                    new Route('GET', '/posts', 'postsHandler'),
                    new Route('GET', '/posts/{id}', 'postHandler')
                ],
                new ServerRequest('GET', '/posts/good-post31_'),
                [
                    'route' => new Route('GET', '/posts/{id}', 'postHandler'),
                    'vars' => ['id' => 'good-post31_']
                ]
            ],
            [
                [new Route('GET', '/{id:(ab|cd){5,}}', 'handler')],
                new ServerRequest('GET', '/cdababababcdababcdabcd'),
                [
                    'route' => new Route('GET', '/{id:(ab|cd){5,}}', 'handler'),
                    'vars' => ['id' => 'cdababababcdababcdabcd']
                ]
            ]
        ];
    }

    /**
     * @return void
     * @throws RouteNotFoundException
     * @throws InvalidCharacterException
     */
    public function testFindRoute2(): void
    {
        $router = new Router();

        $router->addRoute('GET', '/', fn() => 'Hello world');
        $router->addRoute('GET', '/posts/{id:\d+}', fn(int $id) => "Post with id $id");
        $router->addRoute('POST', '/posts', 'createPost');
        $route = $router->addRoute('DELETE', '/posts/{id:\d+}', 'deletePost');

        $result = $router->findRoute(new ServerRequest('DELETE', '/posts/51'));

        $this->assertEquals($result, ['route' => $route, 'vars' => ['id' => '51']]);
    }

    /**
     * @return void
     * @throws RouteNotFoundException
     * @throws InvalidCharacterException
     */
    public function testFindRoute3(): void
    {
        $router = new Router();
        $request = new ServerRequest('PUT', '/news/breaking-news-22_02');

        $router->addRoute('GET', '/', fn() => 'Hello world');
        $route = $router->addRoute('PUT', '/news/{id:[a-zA-Z-_0-9]{0,30}}', 'editNewsItem');

        $result = $router->findRoute($request);

        $this->assertEquals($result, ['route' => $route, 'vars' => ['id' => 'breaking-news-22_02']]);

        $router->addRoute('DELETE', '/news/{id:[a-zA-Z-_0-9]{0,30}}', 'deleteNewsItem');
        $router->addRoute(
            'GET',
            '/news/{id:[a-zA-Z-_0-9]{0,30}}/sources/{sourceId}',
            'getNewsItemSource'
        );
        $router->addRoute('GET', '/authors', 'authorsList');

        $router->findRoute($request);

        $this->assertEquals($result, ['route' => $route, 'vars' => ['id' => 'breaking-news-22_02']]);
    }

    /**
     * @dataProvider findRouteNotFoundProvider
     * @param array $routes
     * @param RequestInterface $request
     * @return void
     * @throws RouteNotFoundException
     * @throws InvalidCharacterException
     */
    public function testFindRouteNotFound(array $routes, RequestInterface $request): void
    {
        $this->expectException(RouteNotFoundException::class);

        $router = new Router();

        foreach ($routes as $route) {
            $router->addRoute($route->method, $route->pattern, $route->handler);
        }

        $router->findRoute($request);
    }

    /**
     * @return array|array[]
     */
    public function findRouteNotFoundProvider(): array
    {
        return [
            [
                [
                    new Route('GET', '/users', 'usersList'),
                    new Route('GET', '/users/{id:\d+}', 'getUser'),
                    new Route('POST', '/users', 'createUser')
                ],
                new ServerRequest('GET', '/')
            ],
            [
                [
                    new Route('GET', '/users', 'usersList'),
                    new Route('GET', '/users/{id:\d+}', 'getUser'),
                    new Route('POST', '/users', 'createUser')
                ],
                new ServerRequest('GET', '/users/john')
            ]
        ];
    }
}
