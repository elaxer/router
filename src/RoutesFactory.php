<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\PatternParserInterface;

/**
 * Routes factory
 */
class RoutesFactory implements RoutesFactoryInterface
{
    /**
     * @var PatternParserInterface pattern parser
     */
    protected PatternParserInterface $patternParser;

    /**
     * @param PatternParserInterface $patternParser pattern parser
     */
    public function __construct(PatternParserInterface $patternParser)
    {
        $this->patternParser = $patternParser;
    }

    /**
     * {@inheritDoc}
     */
    public function createRoute(?array $methods, string $pattern, $handler, ?string $name = null): RouteInterface
    {
        return new Route($this->patternParser, $methods, $pattern, $handler, $name);
    }
}
