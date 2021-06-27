<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\{ParameterInterface, PatternParserInterface};

/**
 * Contains route information
 */
class Route implements RouteInterface
{
    /**
     * @var array|null route method. Can be null if the route doesn't care which method
     */
    private ?array $methods;

    /**
     * @var string route pattern. May contain regular expressions in braces "{}"
     */
    private string $pattern;

    /**
     * @var mixed route handler. May contain any type of value
     */
    private $handler;

    /**
     * @var string|null route name, identifier
     */
    private ?string $name;

    /**
     * @var PatternParserInterface
     */
    protected PatternParserInterface $patternParser;

    /**
     * @param PatternParserInterface $patternParser
     * @param array|null $methods route method. Can be null if the route doesn't care which method
     * @param string $pattern route pattern. May contain regular expressions in braces "{}"
     * @param mixed $handler route handler. May contain any type of value
     * @param string|null $name route name, identifier
     */
    public function __construct(
        PatternParserInterface $patternParser,
        ?array $methods,
        string $pattern,
        $handler,
        ?string $name = null
    ) {
        $this->patternParser = $patternParser;
        $this->name = $name;
        $this->handler = $handler;
        $this->pattern = $pattern;
        $this->methods = $methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function createPath(array $parameters = []): string
    {
        $patternParameters = $this->patternParser->getParameters($this->getPattern());
        $patternParametersKeys = array_map(
            fn(ParameterInterface $parameter): string => $parameter->getName(),
            $patternParameters
        );
        if (
            array_diff(array_keys($parameters), $patternParametersKeys)
            !== array_diff($patternParametersKeys, array_keys($parameters))
        ) {
            throw new PathCreatingException('The passed parameters don\'t match the parameters in the pattern');
        }

        $createdPath = $this->getPattern();
        foreach ($patternParameters as $patternParameter) {
            $parameterValue = (string)$parameters[$patternParameter->getName()];
            if (preg_match('~^' . $patternParameter->getRegexp() . '$~', $parameterValue) === 0) {
                throw new PathCreatingException("Parameter \"{$patternParameter->getName()}\" with value "
                    . "\"{$parameterValue}\" does not match the regular expression from the pattern "
                    . "\"{$patternParameter->getRegexp()}\"");
            }

            $createdPath = str_replace((string)$patternParameter, $parameterValue, $createdPath);
        }

        return $createdPath;
    }
}
