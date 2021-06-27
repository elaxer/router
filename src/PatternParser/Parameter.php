<?php

declare(strict_types=1);

namespace Elaxer\Router\PatternParser;

/**
 * An abstraction class over a parameter. Contains name and regexp and methods for working with them
 */
class Parameter implements ParameterInterface
{
    /**
     * The part of the regular expression that denotes an empty parameter of the pattern
     */
    public const EMPTY_PARAMETER_REGEXP = '[^/]+';

    /**
     * @var string parameter name
     */
    private string $name;

    /**
     * @var string|null parameter regexp. May be null value
     */
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
     * {@inheritDoc}
     */
    public function makeNamedRegexp(): string
    {
        if ($this->regexp === null) {
            return "(?<{$this->name}>" . self::EMPTY_PARAMETER_REGEXP . ')';
        }

        return "(?<{$this->name}>{$this->regexp})";
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
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
