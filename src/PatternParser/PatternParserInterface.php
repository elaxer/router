<?php

declare(strict_types=1);

namespace Elaxer\Router\PatternParser;

/**
 * Provides methods for working with patterns
 */
interface PatternParserInterface
{
    /**
     * Returns an array of extracted parameters from a pattern
     *
     * @param string $pattern
     * @return array<Parameter>
     * @throws ForbiddenCharacterException
     */
    public function getParameters(string $pattern): array;

    /**
     * Creates a regular expression through parameters
     *
     * @param string $pattern full regex
     * @param array<Parameter> $parameters array of parameters
     * @return string
     */
    public function makeRegexpFromPattern(string $pattern, array $parameters): string;

    /**
     * Returns parameters extracted from url path
     *
     * @param string $pattern route pattern
     * @param string $urlPath url path for pattern matching
     * @return array returns parameters extracted from url path
     * @throws ForbiddenCharacterException
     */
    public function extractParametersFromPath(string $pattern, string $urlPath): array;
}
