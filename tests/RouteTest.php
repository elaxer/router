<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests;

use Elaxer\Router\PathCreatingException;
use Elaxer\Router\PatternParser\ForbiddenCharacterException;
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

    /**
     * @covers Route::createPath
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

    public function createPathProvider(): array
    {
        return [
            ['/', new Route(null, '/', null), []],
            ['/posts/527', new Route(null, '/posts/{id:\d+}', null), ['id' => 527]],
            [
                '/posts/527/comments/3',
                new Route(null, '/posts/{id:\d+}/comments/{commentPage:\d+}', null),
                ['commentPage' => 3, 'id' => '527'],
            ],
        ];
    }

    /**
     * @covers Route::createPath
     * @throws PathCreatingException
     * @throws ForbiddenCharacterException
     */
    public function testCreatePathDifferentParametersCount(): void
    {
        $this->expectException(PathCreatingException::class);
        $this->expectExceptionMessage('The passed parameters don\'t match the parameters in the pattern');

        (new Route(null, '/categories/{categoryId}/posts/{postId}', null))->createPath(['categoryId' => 1]);
    }

    /**
     * @covers Route::createPath
     * @throws ForbiddenCharacterException
     * @throws PathCreatingException
     */
    public function testCreatePathParametersMismatching(): void
    {
        $this->expectException(PathCreatingException::class);
        $this->expectExceptionMessage('The passed parameters don\'t match the parameters in the pattern');

        (new Route(null, '/categories/{categoryId}/posts/{postId}', null))
            ->createPath(['categoryId' => 1, 'pstId' => 1]);
    }

    /**
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

        (new Route(null, '/categories/{categoryId:\d+}', null))->createPath(['categoryId' => 'health']);
    }
}
