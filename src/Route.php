<?php

declare(strict_types=1);

namespace Elaxer\Router;

/**
 * Contains route information
 *
 * @package Router
 */
class Route
{
    /**
     * @param array|null $methods route method. Can be null if the route doesn't care which method
     * @param string $pattern route pattern. May contain regular expressions in braces "{}"
     * @param mixed $handler route handler. May contain any type of value
     */
    public function __construct(private ?array $methods, private string $pattern, private mixed $handler) {}

    /**
     * @return array<string>|null route methods
     */
    public function getMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * @return string route pattern
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return mixed route handler
     */
    public function getHandler(): mixed
    {
        return $this->handler;
    }
}
