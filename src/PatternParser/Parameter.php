<?php

declare(strict_types=1);

namespace Elaxer\Router\PatternParser;

/**
 * Class Parameter
 *
 * An abstraction class over a parameter. Contains name and regexp and methods for working with them
 *
 * @package Router\PatternParser
 */
class Parameter
{
    public const EMPTY_PARAMETER_REGEXP = '[^/]+';
    private string $name;
    private ?string $regexp;

    /**
     * @param string $name parameter name
     * @param string|null $regexp parameter regexp. May be null value
     */
    public function __construct(string $name, ?string $regexp)
    {
        $this->regexp = $regexp;
        $this->name = $name;
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

    /**
     * @return string returns parameter name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null returns parameter regexp
     */
    public function getRegexp(): ?string
    {
        return $this->regexp;
    }

    public function __toString(): string
    {
        return $this->makeRouteParameter();
    }
}
