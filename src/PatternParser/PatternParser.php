<?php

declare(strict_types=1);

namespace Elaxer\Router\PatternParser;

/**
 * Provides methods for working with patterns
 *
 * @package Router\PatternParser
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
     * @return array<Parameter>
     * @throws ForbiddenCharacterException
     */
    public static function getParameters(string $pattern): array
    {
        if (mb_strpos($pattern, '~') !== false) {
            throw new ForbiddenCharacterException('Invalid character "~" in parameter');
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
            array_map(fn(Parameter $parameter): string => $parameter->makeRouteParameter(), $parameters),
            array_map(fn(Parameter $parameter): string => $parameter->makeNamedRegexp(), $parameters),
            $pattern
        ) . '$~ui';
    }

    /**
     * Returns parameters extracted from url path
     *
     * @param string $pattern route pattern
     * @param string $urlPath url path for pattern matching
     * @return array returns parameters extracted from url path
     * @throws ForbiddenCharacterException
     */
    public static function extractParametersFromPath(string $pattern, string $urlPath): array
    {
        $parameters = self::getParameters($pattern);
        $regexp = self::makeRegexpFromPattern($pattern, $parameters);

        $matches = [];
        preg_match($regexp, $urlPath, $matches);

        return array_filter($matches, fn (int|string $name): bool => !is_int($name), ARRAY_FILTER_USE_KEY);
    }
}
