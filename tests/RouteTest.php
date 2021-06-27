<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use Elaxer\Router\{PathCreatingException,
    PatternParser\ForbiddenCharacterException,
    PatternParser\PatternParser,
    Route,
    RoutesFactory,
    RoutesFactoryInterface,};
use PHPUnit\Framework\TestCase;

/**
 * @see Route
 */
class RouteTest extends TestCase
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
     * Tests the route constructor
     *
     * @covers Route::getMethods
     * @covers Route::getPattern
     * @covers Route::getHandler
     * @return void
     */
    public function testConstructor(): void
    {
        $route = new Route(new PatternParser(), ['GET'], '/', 'HomeController@index');

        $this->assertEquals('/', $route->getPattern());
        $this->assertEquals('HomeController@index', $route->getHandler());
        $this->assertEquals(['GET'], $route->getMethods());
    }

    /**
     * Tests url path creation based on route template and parameter values
     *
     * @covers       Route::createPath
     * @dataProvider createPathProvider
     * @param string $expectedPath
     * @param Route $route
     * @param array $parameters
     * @throws ForbiddenCharacterException
     * @throws PathCreatingException
     */
    public function testCreatePath(string $expectedPath, Route $route, array $parameters)
    {
        $this->assertSame($expectedPath, $route->createPath($parameters));
    }

    /**
     * @return array[]
     */
    public function createPathProvider(): array
    {
        return [
            ['/', $this->routesFactory->createRoute(null, '/', null), []],
            ['/posts/527', $this->routesFactory->createRoute(null, '/posts/{id:\d+}', null), ['id' => 527]],
            [
                '/posts/527/comments/3',
                $this->routesFactory->createRoute(null, '/posts/{id:\d+}/comments/{commentPage:\d+}', null),
                ['commentPage' => 3, 'id' => '527'],
            ],
        ];
    }

    /**
     * Tests the case where a path is created with a different number of parameters
     *
     * @covers Route::createPath
     * @throws PathCreatingException
     * @throws ForbiddenCharacterException
     */
    public function testCreatePathDifferentParametersCount(): void
    {
        $this->expectException(PathCreatingException::class);
        $this->expectExceptionMessage('The passed parameters don\'t match the parameters in the pattern');

        ($this->routesFactory->createRoute(null, '/categories/{categoryId}/posts/{postId}', null))
            ->createPath(['categoryId' => 1]);
    }

    /**
     * Tests the case where a path is created with mismatched parameter names
     *
     * @covers Route::createPath
     * @throws ForbiddenCharacterException
     * @throws PathCreatingException
     */
    public function testCreatePathParametersMismatching(): void
    {
        $this->expectException(PathCreatingException::class);
        $this->expectExceptionMessage('The passed parameters don\'t match the parameters in the pattern');

        ($this->routesFactory->createRoute(null, '/categories/{categoryId}/posts/{postId}', null))
            ->createPath(['categoryId' => 1, 'pstId' => 1]);
    }

    /**
     * Tests the case where a path is generated with parameters with a mismatching regex value
     *
     * @covers Route::createPath
     * @throws ForbiddenCharacterException
     * @throws PathCreatingException
     */
    public function testCreatePathParametersRegexpMismatching(): void
    {
        $this->expectException(PathCreatingException::class);
        $this->expectExceptionMessage(
            'Parameter "categoryId" with value "health" does not match the regular expression from the pattern "\d+"'
        );

        ($this->routesFactory->createRoute(null, '/categories/{categoryId:\d+}', null))
            ->createPath(['categoryId' => 'health']);
    }
}
