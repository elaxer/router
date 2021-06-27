<?php

declare(strict_types=1);

namespace Elaxer\Router\PatternParser;

/**
 * Provides methods for working with patterns
 *
 * @package Router\PatternParser
 */
class PatternParser implements PatternParserInterface
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
     * {@inheritDoc}
     */
    public function getParameters(string $pattern): array
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
     * {@inheritDoc}
     */
    public function makeRegexpFromPattern(string $pattern, array $parameters): string
    {
        return '~^' . str_replace(
            array_map(fn(Parameter $parameter): string => $parameter->makeRouteParameter(), $parameters),
            array_map(fn(Parameter $parameter): string => $parameter->makeNamedRegexp(), $parameters),
            $pattern
        ) . '$~ui';
    }

    /**
     * {@inheritDoc}
     */
    public function extractParametersFromPath(string $pattern, string $urlPath): array
    {
        $parameters = self::getParameters($pattern);
        $regexp = self::makeRegexpFromPattern($pattern, $parameters);

        $matches = [];
        preg_match($regexp, $urlPath, $matches);

        return array_filter($matches, fn ($name): bool => !is_int($name), ARRAY_FILTER_USE_KEY);
    }
}
