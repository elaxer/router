<?php

declare(strict_types=1);

namespace Elaxer\Router;

/**
 * Class Route
 *
 * Contains route information
 *
 * @package Router
 */
class Route
{
    /**
     * Route method
     *
     * @var string
     */
    public string $method;

    /**
     * Route pattern. May contain regular expressions in braces ({})
     *
     * @var string
     */
    public string $pattern;

    /**
     * Route handler. May contain any type of value
     *
     * @var mixed
     */
    public $handler;

    /**
     * Route constructor
     *
     * @param string $method
     * @param string $pattern
     * @param mixed $handler
     */
    public function __construct(string $method, string $pattern, $handler)
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->handler = $handler;
    }
}
