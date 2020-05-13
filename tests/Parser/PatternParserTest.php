<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Elaxer\Router\Parser\{InvalidCharacterException, Parameter, PatternParser};

/**
 * Class PatternParserTest
 *
 * @package Router\Tests\Parser
 */
class PatternParserTest extends TestCase
{
    /**
     * @dataProvider getParametersProvider
     * @param string $path
     * @param array $expectedParameters
     * @return void
     * @throws InvalidCharacterException
     */
    public function testGetParameters(string $path, array $expectedParameters): void
    {
        $parameters = PatternParser::getParameters($path);
        $this->assertEquals($expectedParameters, $parameters);
    }

    /**
     * @return array|array[]
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
            ['/posts/{   id:  \d{0,10}  }/page/  {page    :[c-x]*\s{4}\w}',
                [new Parameter('id', '\d{0,10}'), new Parameter('page', '[c-x]*\s{4}\w')]
            ],
            ['/books/{name}', [new Parameter('name', null)]]
        ];
    }

    /**
     * @return void
     * @throws InvalidCharacterException
     */
    public function testGetParameter2(): void
    {
        $this->expectException(InvalidCharacterException::class);
        PatternParser::getParameters('/{test:\d+~}');
    }

    /**
     * @dataProvider makeRegexpFromPatternProvider
     * @param string $pattern
     * @param array $parameters
     * @param string $expectedRegexp
     * @return void
     */
    public function testMakeRegexpFromPattern(string $pattern, array $parameters, string $expectedRegexp): void
    {
        $regexp = PatternParser::makeRegexpFromPattern($pattern, $parameters);

        $this->assertEquals($expectedRegexp, $regexp);
    }

    /**
     * @return array|array[]
     */
    public function makeRegexpFromPatternProvider(): array
    {
        return [
            ['/posts/{id:\d+}', [new Parameter('id', '\d+')], '~^/posts/(?<id>\d+)$~ui'],
            ['/posts/{id:\d+}/popular/{page:[0-9]+}', [
                new Parameter('id', '\d+'),
                new Parameter('page', '[0-9]+')
            ], '~^/posts/(?<id>\d+)/popular/(?<page>[0-9]+)$~ui'],
            [
                '/books/{name}', [new Parameter('name', null)],
                '~^/books/(?<name>' . Parameter::EMPTY_PARAMETER_REGEXP . ')$~ui'
            ],
            ['/posts/{id}/{page}',
                [new Parameter('id', null), new Parameter('page', null)],
                '~^/posts/(?<id>' . Parameter::EMPTY_PARAMETER_REGEXP .
                ')/(?<page>' . Parameter::EMPTY_PARAMETER_REGEXP . ')$~ui'
            ],
        ];
    }
}
