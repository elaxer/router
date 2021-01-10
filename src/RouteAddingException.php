<?php

declare(strict_types=1);

namespace Elaxer\Router;

use Exception;

/**
 * Thrown away when a route with the same name already exists
 *
 * @package Elaxer\Router
 */
class RouteAddingException extends Exception
{
}
