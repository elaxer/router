<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\{ForbiddenCharacterException, PatternParser, Parameter};

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
     * @param string|null $name route name, identifier
     */
    public function __construct(
        private ?array $methods,
        private string $pattern,
        private mixed $handler,
        private ?string $name = null
    ) {
    }

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

    /**
     * @return string|null Route name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Creates a path by substituting parameters into the pattern
     *
     * @param array $parameters pattern parameters
     * @return string created path
     * @throws PathCreatingException
     * @throws ForbiddenCharacterException
     */
    public function createPath(array $parameters = []): string
    {
        $patternParameters = PatternParser::getParameters($this->getPattern());
        $patternParametersKeys = array_map(
            fn(Parameter $parameter): string => $parameter->getName(),
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
            $parameterValue = (string) $parameters[$patternParameter->getName()];
            if (preg_match('~^' . $patternParameter->getRegexp() . '$~', $parameterValue) === 0) {
                throw new PathCreatingException("Parameter \"{$patternParameter->getName()}\" with value "
                    . "\"{$parameterValue}\" does not match the regular expression from the pattern "
                    . "\"{$patternParameter->getRegexp()}\"");
            }

            $createdPath = str_replace((string) $patternParameter, $parameterValue, $createdPath);
        }

        return $createdPath;
    }
}
