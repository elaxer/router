<?php

declare(strict_types=1);

namespace Elaxer\Router\Parser;

/**
 * Class Parameter
 *
 * An abstraction class over a parameter. Contains name and regexp and methods for working with them
 *
 * @package Router\Parser
 */
class Parameter
{
    public const EMPTY_PARAMETER_REGEXP = '[^/]+';

    /**
     * Parameter name
     *
     * @var string
     */
    protected string $name;

    /**
     * Parameter regexp. May be null value
     *
     * @var string|null
     */
    protected ?string $regexp;

    /**
     * Parameter constructor
     *
     * @param string $name parameter name
     * @param string|null $regexp parameter regexp
     */
    public function __construct(string $name, ?string $regexp)
    {
        $this->name = $name;
        $this->regexp = $regexp;
    }

    /**
     * Composes a named regular expression
     *
     * If the regexp of the parameter ($this->regexp) is null,
     * then the generated regular expression will be equal to the constant Parameter::EMPTY_PARAMETER_REGEXP
     *
     * @return string named regular expression
     */
    public function makeNamedRegexp(): string
    {
        if ($this->regexp === null) {
            return "(?<{$this->name}>" . self::EMPTY_PARAMETER_REGEXP . ')';
        }

        return "(?<{$this->name}>{$this->regexp})";
    }

    /**
     * Creates a string that matches the router pattern.
     * If regexp is null then it will be omitted as a result
     *
     * @return string
     */
    public function makeRouteParameter(): string
    {
        $parameter = "{{$this->name}";

        if ($this->regexp !== null) {
            $parameter .= ":{$this->regexp}";
        }

        return $parameter . '}';
    }
}