<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Elaxer\Router\PatternParser\ForbiddenCharacterException;

/**
 * Contains route methods
 */
interface RouteInterface
{
    /**
     * @return string[]|null route methods
     */
    public function getMethods(): ?array;

    /**
     * @return string route pattern
     */
    public function getPattern(): string;

    /**
     * @return mixed route handler
     */
    public function getHandler();

    /**
     * @return string|null Route name
     */
    public function getName(): ?string;

    /**
     * Creates a path by substituting parameters into the pattern
     *
     * @param array $parameters pattern parameters
     * @return string created path
     * @throws PathCreatingException thrown when it was not possible to create a path from a route
     * @throws ForbiddenCharacterException thrown when an forbidden character is found in the pattern
     */
    public function createPath(array $parameters = []): string;
}
