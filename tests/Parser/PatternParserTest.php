<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Elaxer\Router\Parser\{ForbiddenCharacterException, Parameter, PatternParser};

/**
 * Class PatternParserTest
 *
 * @package Router\Tests\Parser
 */
class PatternParserTest extends TestCase
{
    /**
     * @covers PatternParser::getParameters
     * @dataProvider getParametersProvider
     * @param string $path
     * @param array $expectedParameters
     * @return void
     * @throws ForbiddenCharacterException
     */
    public function testGetParameters(string $path, array $expectedParameters): void
    {
        $this->assertEquals($expectedParameters, PatternParser::getParameters($path));
    }

    /**
     * @return array
     */
    public function getParametersProvider(): array
    {
        return [
            ['', []],
            ['/', []],
            ['/users', []],
            ['/users/{id:\d+}', [new Parameter('id', '\d+')]],
            ['/users/{id}', [new Parameter('id', null)]],
            ['/posts/{id:[0-9]+ }', [new Parameter('id', '[0-9]+')]],
            ['/posts/   {     id  :    [0-9]+    }   ', [new Parameter('id', '[0-9]+')]],
            ['/posts/{id}/{page:[0-9]+}', [new Parameter('id', null), new Parameter('page', '[0-9]+')]],
            ['/posts/{ id:\w+}/', [new Parameter('id', '\w+')]],
            ['/posts/{ id }/{ page : [a-z]+ }', [new Parameter('id', null), new Parameter('page', '[a-z]+')]],
            ['/posts/{id}{page:[a-z]+}', [new Parameter('id', null), new Parameter('page', '[a-z]+')]],
            ['/posts/{id:\d{0,5}}', [new Parameter('id', '\d{0,5}')]],
            ['/posts/{id:\d{0,5}}/{page:\d{0,5}}', [new Parameter('id', '\d{0,5}'), new Parameter('page', '\d{0,5}')]],
            ['/posts/{id:\d{4}\w+}', [new Parameter('id', '\d{4}\w+')]],
            ['/posts/{id:\d{0,10}\s*\w{,4}}', [new Parameter('id', '\d{0,10}\s*\w{,4}')]],
            ['/posts/{   id:  \d{0,10}  }/page/  {page    :[c-x]*\s{4}\w}', [
                new Parameter('id', '\d{0,10}'),
                new Parameter('page', '[c-x]*\s{4}\w')
            ]],
            ['/books/{name}', [new Parameter('name', null)]],
        ];
    }

    /**
     * @covers PatternParser::getParameters
     * @return void
     * @throws ForbiddenCharacterException
     */
    public function testGetParameterWithForbiddenCharacter(): void
    {
        $this->expectException(ForbiddenCharacterException::class);

        PatternParser::getParameters('/{test:\d+~}');
    }

    /**
     * @covers PatternParser::makeRegexpFromPattern
     * @dataProvider makeRegexpFromPatternProvider
     * @param string $pattern
     * @param array $parameters
     * @param string $expectedRegexp
     * @return void
     */
    public function testMakeRegexpFromPattern(string $pattern, array $parameters, string $expectedRegexp): void
    {
        $this->assertEquals($expectedRegexp, PatternParser::makeRegexpFromPattern($pattern, $parameters));
    }

    /**
     * @return array
     */
    public function makeRegexpFromPatternProvider(): array
    {
        return [
            ['/posts/{id:\d+}', [new Parameter('id', '\d+')], '~^/posts/(?<id>\d+)$~ui'],
            ['/posts/{id:\d+}/popular/{page:[0-9]+}', [
                new Parameter('id', '\d+'),
                new Parameter('page', '[0-9]+'),
            ], '~^/posts/(?<id>\d+)/popular/(?<page>[0-9]+)$~ui'],
            [
                '/books/{name}', [new Parameter('name', null)],
                '~^/books/(?<name>' . Parameter::EMPTY_PARAMETER_REGEXP . ')$~ui',
            ],
            [
                '/posts/{id}/{page}',
                [new Parameter('id', null), new Parameter('page', null)],
                sprintf(
                    '~^/posts/(?<id>%s)/(?<page>%s)$~ui',
                    Parameter::EMPTY_PARAMETER_REGEXP,
                    Parameter::EMPTY_PARAMETER_REGEXP
                ),
            ],
        ];
    }

    /**
     * @covers PatternParser::getParametersValues
     * @dataProvider extractParametersFromPathProvider
     * @param string $urlPath
     * @param string $pattern
     * @param array $expectedParams
     * @throws ForbiddenCharacterException
     */
    public function testExtractParametersFromPath(string $pattern, string $urlPath, array $expectedParams): void
    {
        $this->assertSame($expectedParams, PatternParser::extractParametersFromPath($pattern, $urlPath));
    }

    /**
     * @return array
     */
    public function extractParametersFromPathProvider(): array
    {
        return [
            ['/posts/{id:\d+}/page/{page:[0-9]+}', '/posts/34/page/2', ['id' => '34', 'page' => '2']],
            ['{id:\d+}/{page:[0-9]+}/{any}', '34/42/weg312-13!34%#$2', [
                'id' => '34',
                'page' => '42',
                'any' => 'weg312-13!34%#$2',
            ]],
        ];
    }
}
