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
     * Route constructor
     *
     * @param string $method route method
     * @param string $pattern route pattern. May contain regular expressions in braces ({})
     * @param mixed $handler route handler. May contain any type of value
     */
    public function __construct(private string $method, private string $pattern, private mixed $handler) {}

    /**
     * @return string route method
     */
    public function getMethod(): string
    {
        return $this->method;
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
