<?php

declare(strict_types=1);

namespace Elaxer\Router\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Elaxer\Router\Parser\Parameter;

/**
 * Class ParameterTest
 *
 * @package Router\Tests\Parser
 */
class ParameterTest extends TestCase
{
    /**
     * @dataProvider makeNamedRegexpProvider
     * @param string $name
     * @param string|null $regexp
     * @param string $expectedMadeRegexp
     * @return void
     */
    public function testMakeNamedRegexp(string $name, ?string $regexp, string $expectedMadeRegexp): void
    {
        $parameter = new Parameter($name, $regexp);
        $this->assertEquals($expectedMadeRegexp, $parameter->makeNamedRegexp());
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
     * @dataProvider makeRouteParameterProvider
     * @param string $name
     * @param string|null $regexp
     * @param string $expectedParameter
     * @return void
     */
    public function testMakeRouteParameter(string $name, ?string $regexp, string $expectedParameter): void
    {
        $parameter = new Parameter($name, $regexp);
        $this->assertEquals($expectedParameter, $parameter->makeRouteParameter());
    }

    public function makeRouteParameterProvider(): array
    {
        return [
            ['id', '\d+', '{id:\d+}'],
            ['page', '[0-9]+\s*', '{page:[0-9]+\s*}'],
            ['name', null, '{name}']
        ];
    }
}
