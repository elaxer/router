<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use Elaxer\Router\{PatternParser\ForbiddenCharacterException,
    PatternParser\PatternParser,
    Route,
    RouteAddingException,
    RoutesCollection,
    RoutesFactory,
    RoutesFactoryInterface,
    RoutesFinder,};
use PHPUnit\Framework\TestCase;

/**
 * @see RoutesFinder
 */
class RoutesFinderTest extends TestCase
{
    /**
     * @var RoutesFactoryInterface routes factory
     */
    private RoutesFactoryInterface $routesFactory;

    /**
     * {@inheritDoc}
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->routesFactory = new RoutesFactory(new PatternParser());
    }

    /**
     * Tests finding routes
     *
     * @covers       RoutesFinder::findRoute
     * @dataProvider findRouteProvider
     * @dataProvider findRouteNotFoundProvider
     * @param string $urlPath
     * @param string $method
     * @param Route|null $expectedRoute
     * @param array $routes
     * @return void
     * @throws ForbiddenCharacterException
     * @throws RouteAddingException
     */
    public function testFindRoute(string $urlPath, string $method, ?Route $expectedRoute, array $routes): void
    {
        $collection = new RoutesCollection();
        foreach ($routes as $route) {
            $collection->addRoute($route);
        }

        $this->assertSame(
            $expectedRoute,
            (new RoutesFinder($collection, new PatternParser()))->findRoute($urlPath, $method)
        );
    }

    /**
     * @return iterable
     */
    public function findRouteProvider(): iterable
    {
        $expectedRoute = $this->routesFactory->createRoute(['GET'], '/', 'index');
        yield ['/', 'GET', $expectedRoute, [$expectedRoute]];

        $expectedRoute = $this->routesFactory->createRoute(['DELETE'], '/users/{id:\d{2}}', 'deleteUser');
        yield [
            '/users/12',
            'DELETE',
            $expectedRoute,
            [
                $this->routesFactory->createRoute(['GET'], '/', 'index'),
                $this->routesFactory->createRoute(['POST'], '/users', 'addUser'),
                $expectedRoute,
            ],
        ];

        $expectedRoute = $this->routesFactory->createRoute(null, '/users/{id:\d{2}}', 'deleteUser');
        yield [
            '/users/12',
            'DELETE',
            $expectedRoute,
            [
                $expectedRoute,
                $this->routesFactory->createRoute(null, '/users/{id:\d{2}}', 'deleteUser'),
                $this->routesFactory->createRoute(['POST'], '/users', 'addUser'),
            ],
        ];

        $expectedRoute = $this->routesFactory->createRoute(null, '/users/{id:\d+}', 'deleteUser');
        yield ['/users/1', 'DELETE', $expectedRoute, [$expectedRoute]];

        $expectedRoute = $this->routesFactory->createRoute(['GET', 'POST', 'PUT'], '/users/{id:\d+}', 'deleteUser');
        yield ['/users/1', 'GET', $expectedRoute, [$expectedRoute]];

        $expectedRoute = $this->routesFactory->createRoute(['GET'], '/posts/{id}', 'postHandler');
        yield [
            '/posts/good-post31_',
            'GET',
            $expectedRoute,
            [$expectedRoute, $this->routesFactory->createRoute(['GET'], '/posts', 'postsHandler')],
        ];

        $expectedRoute = $this->routesFactory->createRoute(['DELETE'], '/posts/{id:\d+}', 'deletePost');
        yield [
            '/posts/51',
            'DELETE',
            $expectedRoute,
            [
                $this->routesFactory->createRoute(['GET'], '/', fn() => 'Hello world'),
                $this->routesFactory->createRoute(['GET'], '/posts/{id:\d+}', fn(int $id) => "Post with id $id"),
                $this->routesFactory->createRoute(['POST'], '/posts', 'createPost'),
                $expectedRoute,
            ],
        ];
    }

    /**
     * @return iterable
     */
    public function findRouteNotFoundProvider(): iterable
    {
        $this->routesFactory = new RoutesFactory(new PatternParser());

        yield [
            '/',
            'GET',
            null,
            [
                $this->routesFactory->createRoute(['GET'], '/users', 'usersList'),
                $this->routesFactory->createRoute(['GET'], '/users/{id:\d+}', 'getUser'),
                $this->routesFactory->createRoute(['POST'], '/users', 'createUser'),
            ],
        ];
        yield [
            '/users/john',
            'GET',
            null,
            [
                $this->routesFactory->createRoute(['GET'], '/users', 'usersList'),
                $this->routesFactory->createRoute(['GET'], '/users/{id:\d+}', 'getUser'),
                $this->routesFactory->createRoute(['POST'], '/users', 'createUser'),
            ],
        ];
    }

    /**
     * Tests the case where more routes are added after a search, then a second find is performed
     *
     * @covers RoutesFinder::findRoute
     * @return void
     * @throws ForbiddenCharacterException
     * @throws RouteAddingException
     */
    public function testFindRouteTwice(): void
    {
        $this->routesFactory = new RoutesFactory(new PatternParser());

        $urlPath = '/news/breaking-news-22_02';
        $method = 'PUT';

        $routesCollection = (new RoutesCollection())
            ->addRoute($this->routesFactory->createRoute(['GET'], '/', fn() => 'Hello world'))
            ->addRoute($expectedRoute = $this->routesFactory->createRoute(
                ['PUT'],
                '/news/{id:[a-zA-Z-_0-9]{0,30}}',
                'editNewsItem'
            ));

        $router = new RoutesFinder($routesCollection, new PatternParser());

        $routeFound = $router->findRoute($urlPath, $method);
        $this->assertSame($expectedRoute, $routeFound);

        $routesCollection
            ->addRoute(
                $this->routesFactory->createRoute(['DELETE'], '/news/{id:[a-zA-Z-_0-9]{0,30}}', 'deleteNewsItem')
            )
            ->addRoute($this->routesFactory->createRoute(
                ['GET'],
                '/news/{id:[a-zA-Z-_0-9]{0,30}}/sources/{sourceId}',
                'getNewsItemSource'
            ))
            ->addRoute($this->routesFactory->createRoute(['GET'], '/authors', 'authorsList'));

        $routeFound2 = $router->findRoute($urlPath, $method);

        $this->assertSame($expectedRoute, $routeFound2);
    }

    /**
     * Tests finding a route by name
     *
     * @covers       RoutesFinder::findRouteByName
     * @dataProvider findRouteByNameProvider
     * @param string $routerName
     * @param array $routes
     * @param Route|null $expectedRoute
     * @throws RouteAddingException
     */
    public function testFindRouteByName(string $routerName, array $routes, ?Route $expectedRoute): void
    {

        $routesCollection = new RoutesCollection();
        foreach ($routes as $route) {
            $routesCollection->addRoute($route);
        }

        $this->assertSame(
            $expectedRoute,
            (new RoutesFinder($routesCollection, new PatternParser()))->findRouteByName($routerName)
        );
    }

    /**
     * @return iterable
     */
    public function findRouteByNameProvider(): iterable
    {
        $this->routesFactory = new RoutesFactory(new PatternParser());

        $expectedRoute = $this->routesFactory->createRoute(null, '/', null, 'name3');
        yield [
            'name3',
            [
                $this->routesFactory->createRoute(null, '/', null, 'name1'),
                $this->routesFactory->createRoute(null, '/', null, 'name2'),
                $expectedRoute,
                $this->routesFactory->createRoute(null, '/', null, 'name4'),
            ],
            $expectedRoute,
        ];

        yield [
            'name5',
            [
                $this->routesFactory->createRoute(null, '/', null, 'name1'),
                $this->routesFactory->createRoute(null, '/', null, 'name2'),
                $this->routesFactory->createRoute(null, '/', null, 'name3'),
                $this->routesFactory->createRoute(null, '/', null, 'name4'),
            ],
            null,
        ];
    }
}
