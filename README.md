# Elaxer Router
Simple and fast HTTP request router. Requires PSR-7 implementation package
## Installation
Via composer
```bash
composer require elaxer/router
```
## Usage
```php
<?php

use Elaxer\Router\{Parser\PatternParser, Route, Router};

require 'vendor/autoload.php';

// Router creation instance
$router = new Router();

// Adding Routes
$router->addRoute(new Route(['GET'], '/', 'indexHandler'));
// If it doesn't matter what the method should be, then the first parameter must be passed null
$router->addRoute(new Route(null, '/posts/{id}', fn(string $id): string => "Post with id $id"));
// You can define a parameter in a pattern
$router->addRoute(new Route(['GET'], '/news/{id}', fn(string $id): string => "News with id $id"));
// You can define a rule for a parameter as a regular expression
$router->addRoute(new Route(['DELETE'], '/users/{id:\d+}', 'deleteUserItemHandler'));

$path = '/news/13-01-news';
$method = 'GET';
// Finding a route by HTTP request
$foundRoute = $router->findRoute($path, $method);

// If route is found
if ($foundRoute !== null) {
    // Retrieve the handler passed as the third argument in the Route
    $handler = $foundRoute->getHandler();
    // Extracting parameters from url path
    $params = PatternParser::extractParametersFromPath($foundRoute->getPattern(), $path);

    // Further actions with the handler...
    if (is_callable($handler)) {
        // The response will be the string "News with id 13-01-news"
        $response = call_user_func_array($handler, $params);
    }
}
```
