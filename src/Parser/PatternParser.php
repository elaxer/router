<?php

declare(strict_types=1);

namespace Elaxer\Router\Parser;

/**
 * Class PatternParser
 *
 * Provides methods for working with patterns
 *
 * @package Router\Parser
 */
class PatternParser
{
    /**
     * Regular expression for parsing parameters from a pattern
     */
    protected const PARAMETER_REGEXP = '\{\s*
        (?<name>[a-zA-Z_]+)
        (\s*:\s*(?<regexp>(
            (?:\{[^{}]+?\})*[^{}]+?(?:\{[^{}]+?\})*
        )*)?)?
    \s*\}';

    /**
     * Returns an array of extracted parameters from a pattern
     *
     * @param string $pattern
     * @return array
     * @throws InvalidCharacterException
     */
    public static function getParameters(string $pattern): array
    {
        if (mb_strpos($pattern, '~') !== false) {
            throw new InvalidCharacterException('Invalid character "~" in parameter');
        }

        $matches = [];
        preg_match_all('~' . self::PARAMETER_REGEXP . '~xui', $pattern, $matches);

        $parameters = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $regexp = $matches['regexp'][$i] === '' ? null : trim($matches['regexp'][$i]);
            $parameters[] = new Parameter($matches['name'][$i], $regexp);
        }

        return $parameters;
    }

    /**
     * Creates a regular expression through parameters
     *
     * @param string $pattern full regex
     * @param array $parameters array of parameters
     * @return string
     */
    public static function makeRegexpFromPattern(string $pattern, array $parameters): string
    {
        return '~^' . str_replace(
            array_map(fn(Parameter $parameter) => $parameter->makeRouteParameter(), $parameters),
            array_map(fn(Parameter $parameter) => $parameter->makeNamedRegexp(), $parameters),
            $pattern
        ) . '$~ui';
    }
}
