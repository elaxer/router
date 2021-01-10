<?php

declare(strict_types=1);

namespace Elaxer\Router\PatternParser;

use Exception;

/**
 * Thrown when an forbidden character is found in the pattern
 *
 * @package Router\PatternParser
 */
class ForbiddenCharacterException extends Exception
{
}
