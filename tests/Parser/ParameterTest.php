<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests\Parser;

use Elaxer\Router\PatternParser\Parameter;
use PHPUnit\Framework\TestCase;

/**
 * @see Parameter
 */
class ParameterTest extends TestCase
{
    /**
     * Tests the creation of a regex named
     *
     * @covers       Parameter::makeNamedRegexp
     * @dataProvider makeNamedRegexpProvider
     * @param string $name
     * @param string|null $regexp
     * @param string $expectedMadeRegexp
     * @return void
     */
    public function testMakeNamedRegexp(string $name, ?string $regexp, string $expectedMadeRegexp): void
    {
        $this->assertEquals($expectedMadeRegexp, (new Parameter($name, $regexp))->makeNamedRegexp());
    }

    /**
     * @return array
     */
    public function makeNamedRegexpProvider(): array
    {
        return [
            ['id', '\d+', '(?<id>\d+)'],
            ['page', '[0-9]+\s*', '(?<page>[0-9]+\s*)'],
            ['name', null, '(?<name>' . Parameter::EMPTY_PARAMETER_REGEXP . ')']
        ];
    }

    /**
     * Tests the creation of a route parameter
     *
     * @covers       Parameter::makeRouteParameter
     * @dataProvider makeRouteParameterProvider
     * @param string $name
     * @param string|null $regexp
     * @param string $expectedParameter
     * @return void
     */
    public function testMakeRouteParameter(string $name, ?string $regexp, string $expectedParameter): void
    {
        $this->assertEquals($expectedParameter, (new Parameter($name, $regexp))->makeRouteParameter());
    }

    /**
     * @return array
     */
    public function makeRouteParameterProvider(): array
    {
        return [
            ['id', '\d+', '{id:\d+}'],
            ['page', '[0-9]+\s*', '{page:[0-9]+\s*}'],
            ['name', null, '{name}']
        ];
    }
}
