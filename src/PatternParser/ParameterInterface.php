<?php

declare(strict_types=1);

namespace Elaxer\Router\PatternParser;

/**
 * Provides methods for the route template parameter
 */
interface ParameterInterface
{
    /**
     * Composes a named regular expression
     *
     * If no regular expression is specified in the pattern, then the regular expression must mean any value
     *
     * @return string named regular expression
     */
    public function makeNamedRegexp(): string;

    /**
     * Creates a string that matches the router pattern.
     * If regexp is null then it will be omitted as a result
     *
     * @return string
     */
    public function makeRouteParameter(): string;

    /**
     * @return string returns parameter name
     */
    public function getName(): string;

    /**
     * @return string|null returns parameter regexp
     */
    public function getRegexp(): ?string;

    /**
     * @return string
     * @see makeRouteParameter
     */
    public function __toString(): string;
}
